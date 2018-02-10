<template>
    <button
            v-bind:class="{
                btn: true,
                'btn-default': !active,
                'btn-primary': active
            }"
            v-text="text"
            @click="toggle">

    </button>
</template>

<script>
export default {
    props: ['initActive'],

    data() {
        return {
            active: this.initActive
        };
    },

    computed: {
        text() {
            return this.active ? 'Unsubscribe' : 'Subscribe';
        }
    },

    methods: {
        toggle() {
            const requestType = this.active ? 'delete': 'post';
            axios[requestType](location.pathname + '/subscriptions')
                .then(r => {
                    this.active = !this.active;
                });
        }
    }
}
</script>