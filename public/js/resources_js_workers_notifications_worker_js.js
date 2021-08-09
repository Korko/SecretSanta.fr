/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************************************!*\
  !*** ./resources/js/workers/notifications.worker.js ***!
  \******************************************************/
/*export const listen = async subUrl => {
    // This will be called only once when the service worker is activated.
    try {
        const applicationServerKey = urlB64ToUint8Array(
            'BJ5IxJBWdeqFDJTvrZ4wNRu7UY2XigDXjgiUBYEYVXDudxhEs0ReOJRBcBHsPYgZ5dyV8VjyqzbQKS8V7bUAglk'
        );
        const options = { applicationServerKey, userVisibleOnly: true };
        const subscription = await self.registration.pushManager.subscribe(options);
        const response = await saveSubscription(subUrl, subscription);
        console.log(response);
    } catch (err) {
        console.log('Error', err);
    }
}

// urlB64ToUint8Array is a magic function that will encode the base64 public key
// to Array buffer which is needed by the subscription option
const urlB64ToUint8Array = base64String => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// saveSubscription saves the subscription to the backend
const saveSubscription = async (subUrl, subscription) => {
    const response = await fetch(subUrl, {
        method: 'post',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(subscription),
    });
    return response.json();
}*/
// Register event listener for the 'push' event.
self.addEventListener('push', function (event) {
  // Keep the service worker alive until the notification is created.
  event.waitUntil( // Show a notification with title 'ServiceWorker Cookbook' and body 'Alea iacta est'.
  // Set other parameters such as the notification language, a vibration pattern associated
  // to the notification, an image to show near the body.
  // There are many other possible options, for an exhaustive list see the specs:
  //   https://notifications.spec.whatwg.org/
  self.registration.showNotification('ServiceWorker Cookbook', {
    lang: 'la',
    body: 'Alea iacta est',
    icon: 'caesar.jpg',
    vibrate: [500, 100, 500]
  }));
});
/******/ })()
;