import { ref, unref } from 'vue'

export function useFetch({ url, data }) {
  const data = ref(null)
  const error = ref(null)

  return new Promise((resolve, reject) => {
    
  });

  fetch(unref(url))
    .then((res) => res.json())
    .then((json) => (data.value = json))
    .catch((err) => (error.value = err))

  return { data, error }
}

if (!this.sending && !this.sent && !this.isInvalid) {
    this.sending = true;

    return post(url, options.data)
        .then(response => {
            this.sending = false;

            if(!this.autoReset) {
                this.sent = true;
            }

            (options.success || options.then || function() {})(response);
            (options.complete || options.finally || function() {})();

            this.$emit('success', response);

            if(this.autoReset) {
                this.onReset();
            }
        })
        .catch(response => {
            this.sending = false;

            var callback;
            if(callback = (options.error || options.catch)) {
                callback(response);
            }

            var callback2;
            if(callback2 = (options.complete || options.finally)) {
                callback2();
            }

            if(!callback && !callback2 && response?.errors.length > 0) {
                this.$dialog
                    .alert(this.$t('form.internalError'));
            }

            this.$emit('error', response?.errors);
        });
}
