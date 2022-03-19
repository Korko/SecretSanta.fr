<script>
    export default {
        props: {
            direction: {
                type: String,
                validator: function (value) {
                    return ['top', 'left', 'bottom', 'right'].indexOf(value) !== -1
                },
                default: 'right'
            }
        }
    };
</script>

<template>
    <div class="tip-wrapper">
        <span class="tip-handler"><slot name="default"></slot></span>
        <div class="tip-content" :class="direction">
            <slot name="tooltip"></slot><i></i>
        </div>
    </div>
</template>

<style scoped>
    .tip-wrapper {
        display: inline-block;
        position: relative;
        text-align: left;
    }
    .tip-wrapper .tip-handler:deep(*) {
        text-decoration: underline dotted;
    }

    .tip-content h3 {margin: 12px 0;}

    .tip-content {
        min-width: 300px;
        max-width: 400px;
        color: #EEEEEE;
        background-color: #444444;
        font-weight: normal;
        font-size: 13px;
        border-radius: 8px;
        position: absolute;
        z-index: 99999999;
        box-sizing: border-box;
        box-shadow: 0 1px 8px rgba(0,0,0,0.5);
        visibility: hidden; opacity: 0; transition: opacity 0.8s;
        padding: 0;
    }
    .tip-content.right {
        top: 50%;
        left: 100%;
        margin-left: 20px;
        transform: translate(0, -50%);
    }
    .tip-content.left {
        top: 50%;
        right: 100%;
        margin-right: 20px;
        transform: translate(0, -50%);
    }
    .tip-content.top {
        top: -20px;
        left: 50%;
        transform: translate(-30%,-100%);
    }
    .tip-content.bottom {
        top: 40px;
        left: 50%;
        transform: translate(-50%, 0);
    }

    .tip-wrapper:hover .tip-content {
        visibility: visible; opacity: 1;
    }

    .tip-content:deep(.text-content) {
        padding: 10px 20px;
    }

    .tip-content img {
        width: 400px;
        border-radius: 8px 8px 0 0;
    }

    .tip-content.right i {
        position: absolute;
        top: 50%;
        right: 100%;
        margin-top: -12px;
        width: 12px;
        height: 24px;
        overflow: hidden;
    }
    .tip-content.right i::after {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        left: 0;
        top: 50%;
        transform: translate(50%,-50%) rotate(-45deg);
        background-color: #444444;
        box-shadow: 0 1px 8px rgba(0,0,0,0.5);
    }

    .tip-content.left i {
        position: absolute;
        top: 50%;
        left: 100%;
        margin-top: -12px;
        width: 12px;
        height: 24px;
        overflow: hidden;
    }
    .tip-content.left i::after {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        left: 0;
        top: 50%;
        transform: translate(-50%,-50%) rotate(-45deg);
        background-color: #444444;
        box-shadow: 0 1px 8px rgba(0,0,0,0.5);
    }

    .tip-content.top i {
        position: absolute;
        top: 100%;
        left: 30%;
        margin-left: -12px;
        width: 24px;
        height: 12px;
        overflow: hidden;
    }
    .tip-content.top i::after {
        content: '';
        position: absolute;
        width: 15px;
        height: 15px;
        left: 50%;
        transform: translate(-50%,-50%) rotate(45deg);
        background-color: #444444;
        box-shadow: 0 1px 8px rgba(0,0,0,0.5);
    }

    .tip-content.bottom i {
        position: absolute;
        bottom: 100%;
        left: 50%;
        margin-left: -12px;
        width: 24px;
        height: 12px;
        overflow: hidden;
    }
    .tip-content.bottom i::after {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        left: 50%;
        transform: translate(-50%,50%) rotate(45deg);
        background-color: #444444;
        box-shadow: 0 1px 8px rgba(0,0,0,0.5);
    }
</style>