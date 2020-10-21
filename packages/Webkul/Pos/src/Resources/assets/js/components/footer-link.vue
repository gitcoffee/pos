<template>
    <div class="bagisto-footer-container" v-if="isShowLink" >
        <div class="admin-link">
            <a  v-if="adminLink" :href="adminLink" target="_blank">Pos Admin Demo</a>
            <a  v-if="storeLink" :href="storeLink" target="_blank">Download</a>
        </div>
        <i :class="[faClass, toggleClass]" @click="toggleFooterPanel"></i>
    </div>
</template>

<script>
    export default {
        props: {
            showLink: {
                type: Boolean,
                required: true,
            },

            adminLink: {
                type: String,
                required: true,
                default: window.app_base_url + 'admin/login'
            },
            storeLink: {
                type: String,
                default: ''
            }
        },

        data () {
            return {
                faClass: 'fa',
                toggleClass: ''
            };
        },
        created () {
            this.openFooterPanel();
        },

        computed: {
            isShowLink () {
                return this.showLink;
            }
        },
        methods: {
            openFooterPanel () {
                this.toggleClass = 'fa-chevron-down';
            },

            toggleFooterPanel () {
                var panelBottom = $('.bagisto-footer-container').css('bottom');
                
                if(panelBottom == '-40px') {
                    this.toggleClass = 'fa-chevron-down';
                    $('.bagisto-footer-container').css('bottom', '0px');
                } else {
                    this.toggleClass = 'fa-chevron-up';
                    $('.bagisto-footer-container').css('bottom', '-40px');
                }
            }
        }
    }
</script>