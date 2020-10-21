<template>
    <div :id="id" v-if="isOpen">
        <div id="loader">
            <span class="pos-loader-overlay"></span>
            <div class="cp-spinner cp-balls"></div>

            <div class="pos-progress-bar-container" v-if="isActive">
                <div class="pos-progress-note" v-if="progressData.noteInfo.length">
                    <i class="fa fa-info-circle"></i>
                    {{ progressData.noteInfo }}
                </div>

                <div class="pos-progress">
                    <div class="pos-progress-bar"  v-bind:style="{'width': progressWidth + '%'}" v-if="progressWidth">
                        <span class="percentage">{{ numberRound(progressWidth) }}</span>
                    </div>
                </div>
                <div class="pos-progress-text" v-if="progressData.textContent.length">
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
            </div>
        </div>
        
    </div>
</template>


<script>

    export default {
        props: ['id', 'isOpen', 'isActive', 'progressWidth', 'progressData'],
        methods: {
            deactiveProgress() {
                this.show_progress_bar = 0;
            },
            numberRound(num) {
                return Math.round(num) + '%';
            }
        }
    }
</script>

