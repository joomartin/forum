export default {
    data() {
        return {
            items: []
        };
    },

    methods: {
        add(item) {
            this.items = [
                ...this.items,
                item
            ];

            this.$emit('added');
        },

        remove(id) {
            this.items = this.items.filter(r => r.id != id);
            this.$emit('removed');
        }
    }
}