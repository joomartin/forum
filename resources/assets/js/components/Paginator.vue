<template>
    <ul class="pagination" v-if="shouldPaginate">
        <li
            v-bind:class="{'page-item': true, prev: true, disabled: !prevUrl}">
            <a class="page-link" href="#" tabindex="-1" rel="prev" @click.prevent="prev">&laquo;</a>
        </li>

        <li
                v-for="p in pagesArray"
                v-bind:class="{
                    'page-item': true,
                    active: p === page
                }"
                v-bind:id="'page-' + p">
            <a class="page-link" href="#" tabindex="-1" @click.prevent="page = p" v-text="p"></a>
        </li>

        <li
            v-bind:class="{'page-item': true, next: true, disabled: !nextUrl}">
            <a class="page-link" href="#" rel="next" @click.prevent="next">&raquo;</a>
        </li>
    </ul>
</template>

<script>
    import _ from 'lodash';

    export default {
        props: {
            dataSet: {
                type: Object
            },
            maxPage: {
                type: Number,
                default: 10
            }
        },

        data() {
            return {
                page: 1,
                prevUrl: false,
                nextUrl: false
            };
        },

        watch: {
            dataSet() {
                this.page = this.dataSet.current_page;
                this.prevUrl= this.dataSet.prev_page_url;
                this.nextUrl= this.dataSet.next_page_url;
            },

            page() {
                this.broadcast().updateUrl();
            }
        },

        computed: {
            shouldPaginate() {
                return !!this.prevUrl || !!this.nextUrl;
            },

            pagesArray() {
                if (this.isDataSetFits()) {
                    return _.range(this.dataSet.last_page).map(x => x + 1);
                }

                if (this.isPageAtBeginning()) {
                    return [
                        ..._.range(1, this.maxPage),
                        this.dataSet.last_page
                    ];
                }

                if (this.isPageAtEnd()) {
                    return [
                        1,
                        ...this.getLastPagesBy(this.maxPage - 1)
                    ];
                }

                const quarter = Math.round(this.maxPage / 4);

                const leftRange = _.range(2, this.page);
                const rightRange = _.range(this.page + 1, this.dataSet.last_page);

                let leftSlice = leftRange.slice(quarter * -1);
                let rightSlice = rightRange.slice(0, quarter + (quarter - leftSlice.length));

                if (rightSlice.length === 1) {
                    leftSlice = leftRange.slice((quarter + 1) * -1);
                    rightSlice = rightRange.slice(0, quarter);
                }

                return [
                    1,
                    ...leftSlice,
                    this.page,
                    ...rightSlice,
                    this.dataSet.last_page
                ];
            }
        },

        methods: {
            broadcast() {
                return this.$emit('paged', this.page);
            },

            updateUrl() {
                history.pushState(null, null, `?page=${this.page}`);
            },

            prev() {
                if (this.page === 1) return;
                this.page--;
            },

            next() {
                if (this.page === this.dataSet.last_page) return;
                this.page++;
            },

            isDataSetFits() {
                return this.dataSet.last_page <= this.maxPage;
            },

            isPageAtBeginning() {
                return this.page <= 2;
            },

            isPageAtEnd() {
                return this.page >= this.dataSet.last_page - 1;
            },

            getLastPagesBy(n) {
                return _.range(this.dataSet.last_page + 1).slice(n * -1)
            }
        }
    }
</script>