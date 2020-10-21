<template>
    <div class="bagisto-progress-bar-container" v-if="isProgressActive">
        <div class="bagisto-progress-note" v-if="progressData.noteInfo.length">
            <i class="fa fa-info-circle"></i>
            {{ progressData.noteInfo }}
        </div>

        <div class="bagisto-progress">
            <div class="bagisto-progress-bar"  v-bind:style="{'width': progressWidth + '%'}" v-if="progressWidth">
                <span class="percentage">{{ numberRound(progressWidth) }}</span>
            </div>
        </div>
        <div class="bagisto-progress-text" v-if="progressData.textContent.length">
            <span class="success" v-if="progressData.textContentType == 'success'">
                {{ progressData.textContent }}
            </span>
            <span class="warning" v-if="progressData.textContentType == 'warning'">
                {{ progressData.textContent }}
            </span>
            <span class="danger" v-if="progressData.textContentType == 'danger'">
                {{ progressData.textContent }}
            </span>
            <span class="information" v-if="progressData.textContentType == 'information'">
                {{ progressData.textContent }}
            </span>
        </div>

        <div class="bagisto-message-container" v-if="progressData.messageStatus">
            <div class="message" v-for="message in messageContent">
                <div class="msg-success" v-if="message.class == 'success'">
                    {{ message.text }}
                </div>
                <div class="msg-warning" v-if="message.class == 'warning'">
                    {{ message.text }}
                </div>
                <div class="msg-danger" v-if="message.class == 'danger'">
                    {{ message.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['isActive', 'progressWidth', 'progressData', 'messageContent'],
        computed: {
            isProgressActive () {
                return this.isActive;
            }
        },
        created() {
            this.deactiveProgress();
        },
        methods: {
            deactiveProgress() {
                this.isActive = 0;
            },
            numberRound(num) {
                return Math.round(num) + '%';
            }
        }
    }

</script>
