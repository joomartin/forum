<template>
    <div :id="'reply-' + id" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/' + data.owner.name"
                        v-text="data.owner.name">
                    </a> said <span v-text="ago"></span>
                </h5>

                <div v-if="signedIn">
                    <favorite :reply="data"></favorite>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body" required></textarea>
                    </div>

                    <button class="btn btn-xs btn-primary" type="submit">Update</button>
                    <button class="btn btn-xs btn-link" @click.prevent="cancel" type="button">Cancel</button>
                </form>

            </div>

            <div v-else v-html="body"></div>
        </div>

        <div class="panel-footer level" v-if="data.can.update">
            <button class="btn btn-xs btn-default mr-1" @click="editing = true">Edit</button>
            <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
        </div>
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: ['data'],

        components: {Favorite},

        computed: {
            signedIn() {
                return window.App.signedIn;
            },

            ago() {
                return moment(this.data.created_at).fromNow();
            }
        },

        data() {
            return {
                editing: false,
                body: this.data.body,
                id: this.data.id
            };
        },

        methods: {
            update() {
                const success = (msg) => {
                    this.editing = false;
                    this.data.body = this.body;

                    flash(msg);
                };

                if (this.data.body === this.body) {
                    success('Reply not changed...');
                    return;
                }

                axios.patch(`/replies/${this.id}`, {
                    body: this.body
                })
                .then(() => {
                    success('Reply updated');
                })
                .catch(error => {
                    flash(error.response.data, 'danger');
                });
            },

            destroy() {
                axios.delete(`/replies/${this.id}`);

                this.$emit('deleted', this.id);
                flash('Your reply has been deleted.');
            },

            cancel() {
                this.editing = false;
                this.body = this.data.body;
            }
        }
    }
</script>