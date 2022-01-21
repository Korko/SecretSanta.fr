<template>
    <div>
        Coucou
    </div>
</template>

<script>
    import Moment from 'moment';

    import Echo from '../partials/echo.js';

    export default {
        props: {
            draw: {
                type: Object,
                required: true
            }
        },

        created() {
            Echo.channel('pending_draw.'+this.draw.id)
                .listen('.status.update', data => {
                    this.draw = {...this.draw, ...data};
                });
        }
    };
</script>

<style scoped>

</style>
