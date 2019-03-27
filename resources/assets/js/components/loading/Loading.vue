<template>
    <span v-if="show">
        <i class="far fa-refresh fa-spin fa-fw" v-bind:class="sizeClass"></i>
    </span>
</template>

<script>
    export default {
        name: 'loading',
        props: {
            show: {
                type: Boolean,
                default: false
            },
            size: {
                type: String
            }

        },
        data: function () {
            return {
                sizes: {
                    '2': 'fa-2x'
                }
            }
        },
        computed: {
            sizeClass: function () {
                if (typeof this.sizes[this.size] == 'string') {
                    var cls = this.sizes[this.size];
                    return {
                        cls: true
                    };
                }

                return {};
            }
        }
    }
</script>


