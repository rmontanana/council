<template>
    <button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>
    export default {
        props: ['active'],

        computed: {
            classes() {
                return ['btn', this.active ? 'btn-danger' : 'btn-primary'];
            }
        },

        methods: {
            subscribe() {
                axios[
                    (this.active ? 'delete' : 'post')
                ](location.pathname + '/subscriptions');

                this.active = ! this.active;

                flash(this.active ? 'Subscribed' : 'Unsuscribed');
            }
        }
    }
</script>