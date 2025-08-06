import CryptoJS from 'crypto-js'

export function useEncryption() {
    // Générer une clé master
    async function generateMasterKey() {
        const array = new Uint8Array(32)
        crypto.getRandomValues(array)
        return btoa(String.fromCharCode.apply(null, array))
    }

    // Générer une clé individuelle
    async function generateIndividualKey() {
        const array = new Uint8Array(32)
        crypto.getRandomValues(array)
        return btoa(String.fromCharCode.apply(null, array))
    }

    // Chiffrer des données
    async function encryptData(plaintext, key) {
        const encrypted = CryptoJS.AES.encrypt(plaintext, key).toString()
        return encrypted
    }

    // Déchiffrer des données
    async function decryptData(ciphertext, key) {
        const decrypted = CryptoJS.AES.decrypt(ciphertext, key)
        return decrypted.toString(CryptoJS.enc.Utf8)
    }

    // Stocker la clé master dans le localStorage (chiffrée avec une clé dérivée)
    function storeMasterKey(drawUuid, masterKey) {
        const storageKey = deriveStorageKey(drawUuid)
        const encryptedKey = CryptoJS.AES.encrypt(masterKey, storageKey).toString()
        localStorage.setItem(`mk_${drawUuid}`, encryptedKey)
    }

    // Récupérer la clé master
    function getMasterKey(drawUuid) {
        const encryptedKey = localStorage.getItem(`mk_${drawUuid}`)
        if (!encryptedKey) return null

        const storageKey = deriveStorageKey(drawUuid)
        const decrypted = CryptoJS.AES.decrypt(encryptedKey, storageKey)
        return decrypted.toString(CryptoJS.enc.Utf8)
    }

    // Dériver une clé de stockage basée sur l'UUID
    function deriveStorageKey(uuid) {
        // Utiliser des données uniques du navigateur comme sel
        const salt = navigator.userAgent + uuid
        return CryptoJS.SHA256(salt).toString()
    }

    // Nettoyer les clés stockées
    function clearStoredKeys() {
        const keys = Object.keys(localStorage)
        keys.forEach(key => {
            if (key.startsWith('mk_')) {
                localStorage.removeItem(key)
            }
        })
    }

    return {
        generateMasterKey,
        generateIndividualKey,
        encryptData,
        decryptData,
        storeMasterKey,
        getMasterKey,
        clearStoredKeys
    }
}
