<template>
    <div :id="id" v-if="isOpen">
        <div class="pos-modal-overlay"></div>
        <div class="pos-modal-container">
            <div class="modal-header">
                <slot name="header">
                    {{ $t("pos_common_component.modal.header_title") }}
                </slot>
                <i v-if="!showClose" class="icon remove-icon"  @click="closeCommonModal"></i>
            </div>

            <div class="modal-body">
                <slot name="body">
                    {{ $t("pos_common_component.modal.body") }}
                </slot>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['id', 'isOpen', 'showClose'],
        data() {
            return {
                search_opt: window.search_option
            }
        },
        methods: {
            closeCommonModal () {
                this.$root.$set(this.$root.posCommonModal, this.id, false);
                var body = document.querySelector("body");
                    body.classList.remove("pos-modal-open");
                    $("body").find(".pos-navbar-top").removeClass('pos-overlay');
                    $("body").find(".pos-navbar-left").removeClass('pos-overlay');
                if (this.search_opt == 0 && this.id == 'productVariationModal') {
                    $("body").find('#nav-search').val('').focus();
                }
            }
        }
    }
</script>