<script>
    import Replies from '../components/Replies';
    import SubscribeButton from '../components/SubscribeButton';

    export default {
        props: ['initRepliesCount'],

        components: { Replies, SubscribeButton },

        data() {
            return {
                repliesCount: this.initRepliesCount
            };
        }
    }
</script>