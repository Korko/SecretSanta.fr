<script>
    // "Pour : Zoé", "De : ?": a gift tag, an information and never a button
    export default {
        props: {
            label: { type: String, required: true },
            value: { type: String, required: true },
            small: { type: Boolean, default: false },
            // Read instead of the visible text ("De : ?" alone says little)
            ariaLabel: { type: String, default: null },
        },
    };
</script>

<template>
    <span class="pg-gtw" :role="ariaLabel ? 'img' : null" :aria-label="ariaLabel">
        <span class="pg-gt" :class="{ 'pg-gt--sm': small }">
            <span class="pg-gt-in" :aria-hidden="ariaLabel ? 'true' : null">
                <span class="pg-gt-k">{{ label }}</span>
                <span class="pg-gt-v">{{ value }}</span>
            </span>
        </span>
    </span>
</template>
