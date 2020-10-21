<template>
    <div class="pos-modal-container" :id="id" v-if="isModalOpen" >
        <div class="modal-header">
            <slot name="header">
                {{ $t("pos_common_component.modal.header_title") }}
            </slot>
            <i class="icon remove-icon" @click="closeModal"></i>
        </div>

        <div class="modal-body">
            <slot name="body">
                {{ $t("pos_common_component.modal.body") }}
            </slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['id', 'show'],

        created () {
            this.closeModal();
        },

        computed: {
            isModalOpen () {
                this.addClassToBody();

                return this.show;
            }
        },
        methods: {
            closeModal (modal_id) {
                EventBus.$emit('closeModal', modal_id);
            },

            addClassToBody () {
                var body = document.querySelector("body");
            }
        }
    }
</script>