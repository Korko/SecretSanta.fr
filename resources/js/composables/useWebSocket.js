import { ref, onUnmounted } from 'vue'
import { useNotification } from './useNotification'

export function useWebSocket() {
    const channels = ref(new Map())
    const { showNotification } = useNotification()

    function subscribeToChannel(channelName, events) {
        if (channels.value.has(channelName)) {
            return channels.value.get(channelName)
        }

        const channel = window.Echo.channel(channelName)

        // Enregistrer les événements
        Object.entries(events).forEach(([event, handler]) => {
            channel.listen(event, handler)
        })

        channels.value.set(channelName, channel)

        return channel
    }

    function subscribeToPrivateChannel(channelName, events) {
        if (channels.value.has(channelName)) {
            return channels.value.get(channelName)
        }

        const channel = window.Echo.private(channelName)

        Object.entries(events).forEach(([event, handler]) => {
            channel.listen(event, handler)
        })

        channels.value.set(channelName, channel)

        return channel
    }

    function unsubscribeFromChannel(channelName) {
        const channel = channels.value.get(channelName)
        if (channel) {
            window.Echo.leave(channelName)
            channels.value.delete(channelName)
        }
    }

    function unsubscribeFromAll() {
        channels.value.forEach((channel, name) => {
            window.Echo.leave(name)
        })
        channels.value.clear()
    }

    // Nettoyer à la destruction du composant
    onUnmounted(() => {
        unsubscribeFromAll()
    })

    return {
        subscribeToChannel,
        subscribeToPrivateChannel,
        unsubscribeFromChannel,
        unsubscribeFromAll
    }
}
