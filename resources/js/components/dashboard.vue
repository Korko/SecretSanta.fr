<script>
export default {
    props: {

    },
    data() {
        return {

        };
    },
    computed: {
        draws() {
            return JSON.parse(window.localStorage.getItem('secretsanta')) || {};
        }
    },
    watch: {
    }
}
</script>

<template>
    <div>
        <table>
            <tr v-for="(draw, hash) in draws">
                <td>{{ hash }}</td>
                <td>{{ draw.title }}</td>
                <td>{{ draw.creation }}</td>
                <td>{{ draw.expiration }}</td>
                <td>{{ draw.organizerName }}</td>
                <td>{{ draw.links }}</td>
            </tr>
        </table>
    </div>
</template>

<style>
    @import "../../sass/layout.scss";

</style>
