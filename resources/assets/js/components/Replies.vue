<template>
    <div>
        <div v-for="reply in items" :key="reply.id">
            <reply :data="reply" @deleted="remove"></reply>
        </div>

        <paginator :dataSet="dataSet" @paged="fetch"></paginator>

        <new-reply @created="add"></new-reply>
    </div>
</template>

<script>
    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';
    import collection from '../mixins/Collection';

    export default {
        components: { Reply, NewReply },

        mixins: [collection],

        data() {
            return {
                dataSet: {}
            };
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch(page) {
                axios.get(this.url(page))
                    .then(this.refresh);
            },

            url(page) {
                if (!page) {
                    const query = location.search.match(/page=(\d+)/);

                    page = query ? query[1] : 1;
                }
                return `${location.pathname}/replies?page=${page}`;
            },

            refresh(response) {
                this.dataSet = response.data;
                this.items = response.data.data;

                window.scrollTo(0, 0);
            }
        }
    }
</script>