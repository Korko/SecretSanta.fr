import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { useEncryption } from '@/composables/useEncryption'
import { useWebSocket } from '@/composables/useWebSocket'

export const useDrawStore = defineStore('draw', () => {
    const currentDraw = ref(null)
    const participants = ref([])
    const messages = ref([])
    const statistics = ref({})
    const loading = ref(false)
    const error = ref(null)

    const { encryptData, decryptData, generateMasterKey, storeMasterKey, getMasterKey } = useEncryption()
    const { subscribeToChannel, unsubscribeFromChannel } = useWebSocket()

    // Computed
    const acceptedParticipants = computed(() =>
        participants.value.filter(p => p.status === 'accepted')
    )

    const canLaunchDraw = computed(() =>
        currentDraw.value?.status === 'closed_registration' &&
        acceptedParticipants.value.length >= 3
    )

    // Actions
    async function createDraw(drawData) {
        loading.value = true
        error.value = null

        try {
            const masterKey = await generateMasterKey()

            const response = await axios.post('/api/v1/draws', {
                ...drawData,
                title: await encryptData(drawData.title, masterKey),
                description: drawData.description ? await encryptData(drawData.description, masterKey) : null,
                organizer_name: await encryptData(drawData.organizer_name, masterKey),
                organizer_email: await encryptData(drawData.organizer_email, masterKey),
            })

            currentDraw.value = response.data.draw

            // Stocker la clé master localement
            storeMasterKey(currentDraw.value.uuid, response.data.master_key)

            // S'abonner au canal WebSocket
            subscribeToDrawChannel(currentDraw.value.uuid)

            return response.data
        } catch (err) {
            error.value = err.response?.data?.error || 'Failed to create draw'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function loadDraw(uuid) {
        loading.value = true
        error.value = null

        try {
            const masterKey = getMasterKey(uuid)
            if (!masterKey) {
                throw new Error('Master key not found')
            }

            const response = await axios.get(`/api/v1/draws/${uuid}`, {
                headers: {
                    'X-Master-Key': btoa(masterKey)
                }
            })

            currentDraw.value = response.data.draw
            participants.value = response.data.participants

            // Déchiffrer les données
            if (currentDraw.value.title) {
                currentDraw.value.title = await decryptData(currentDraw.value.title, masterKey)
            }

            // S'abonner au canal WebSocket
            subscribeToDrawChannel(uuid)

            return currentDraw.value
        } catch (err) {
            error.value = err.response?.data?.error || 'Failed to load draw'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function addParticipant(participantData) {
        const masterKey = getMasterKey(currentDraw.value.uuid)

        const response = await axios.post(
            `/api/v1/draws/${currentDraw.value.uuid}/participants`,
            participantData,
            {
                headers: {
                    'X-Master-Key': btoa(masterKey)
                }
            }
        )

        participants.value.push(response.data.participant)
        return response.data
    }

    async function launchDraw() {
        loading.value = true

        try {
            const response = await axios.post(`/api/v1/draws/${currentDraw.value.uuid}/launch`)
            currentDraw.value.status = 'processing'
            return response.data
        } finally {
            loading.value = false
        }
    }

    function subscribeToDrawChannel(drawUuid) {
        subscribeToChannel(`draw.${drawUuid}`, {
            '.status': (event) => {
                currentDraw.value.status = event.status

                // Afficher une notification
                if (event.status === 'completed') {
                    showNotification('success', 'Tirage terminé avec succès!')
                } else if (event.status === 'failed') {
                    showNotification('error', 'Le tirage a échoué')
                }
            },
            '.participant.added': (event) => {
                participants.value.push(event.participant)
            },
            '.message.new': (event) => {
                messages.value.unshift(event.message)
            }
        })
    }

    return {
        currentDraw,
        participants,
        messages,
        statistics,
        loading,
        error,
        acceptedParticipants,
        canLaunchDraw,
        createDraw,
        loadDraw,
        addParticipant,
        launchDraw
    }
})
