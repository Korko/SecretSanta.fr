<script>
    export default {
        props: {
            login: {
                type: String,
                required: true
            }
        }
    }
</script>

<template>
    <a class="bmc-button" target="_blank" :href="'https://www.buymeacoffee.com/'+login" rel="noopener noreferrer">
        {{ $t('bmc.button') }}
    </a>
</template>

<style scoped>
    @import "~@fontsource/cookie/index.css";

    .bmc-button {
        height: 60px;
        display: inline-flex;
        align-items: center;
        color: #ffffff;
        background-color: #DD6F36;
        border-radius: 12px;
        border: 1px solid transparent;
        padding: 0 24px 0 calc(24px + 25px + 8px);
        font-size: 22px;
        letter-spacing: 0.6px;
        box-shadow: 0px 1px 2px rgba(190, 190, 190, 0.5);
        -webkit-box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5);
        font-family: 'Cookie', cursive;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        -o-transition: 0.3s all linear;
        -webkit-transition: 0.3s all linear;
        -moz-transition: 0.3s all linear;
        -ms-transition: 0.3s all linear;
        transition: 0.3s all linear;
        background-image: url("../../images/BMC Logo - Black.png");
        background-image: image-set(
            /*url("../../images/BMC Logo - Black.avif") type("image/avif"),
            url("../../images/BMC Logo - Black.webp") type("image/webp"), not relevant in term of sizes*/
            url("../../images/BMC Logo - Black.png") type("image/png")
        );
        background-size: 25px 33px;
        background-repeat: no-repeat;
        background-position: 24px;
    }
</style>