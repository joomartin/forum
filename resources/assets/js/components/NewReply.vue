<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
            <textarea
                    name="body" id="body"
                    rows="5" class="form-control"
                    placeholder="Have something to say?"
                    v-model="body"
                    required></textarea>
            </div>

            <button
                    type="submit"
                    class="btn btn-primary"
                    @click="addReply">Post</button>
        </div>
        <p class="text-center" v-else>Please <a href="/login">sign in</a> to leave a reply</p>
    </div>
</template>

<script>
    import 'jquery.caret';
    import 'at.js';

    export default {
        data() {
            return {
                body: '',
            };
        },

        computed: {
            signedIn() {
                return window.App.signedIn;
            }
        },

        mounted() {
            $('#body').atwho({
                at: '@',
                delay: 750,
                callbacks: {
                    remoteFilter: function (query, callback) {
                        $.getJSON('/api/users', { name: query }, function (usernames) {
                            callback(usernames);
                        });
                    }
                }
            });
        },

        methods: {
            addReply() {
                axios.post(location.pathname + '/replies', { body: this.body })
                    .then(response => {
                        this.body = '';

                        flash('Your reply has been posted');

                        this.$emit('created', response.data);
                    })
                    .catch(error => {
                        flash(error.response.data, 'danger');
                    });
            }
        }
    }
</script>