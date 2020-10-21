<template>
    <li class="modal-category" :title="item.name" :data-category-id="item.id">
        <span class="category-name"  @click="modalFiltering(item)">
            {{ item.name }}
        </span>
        <span class="showSubCategories" v-if="haveChildren" @click="showOrHide">
            <i :class="[show ? 'fa fa-chevron-up' : 'fa fa-chevron-down']" v-if="item.name"></i>
        </span>
        
        <ul class="modal-categories" v-if="haveChildren && show">
            <pos-category-item
                v-for="(child, index) in item.children"
                :key="index"
                :item="child">
            </pos-category-item>
        </ul>
    </li>
</template>


<script>
    export default {
        props: ['item'],
        data() {
            return {
                items_count:0,
                show: false,
            };
        },
        computed: {
            haveChildren() {
                return this.item.children.length ? true : false;
            }
        },

        methods: {
            showOrHide: function() {
                this.show = !this.show;
            },
            modalFiltering(selectedCategory) {
                EventBus.$emit('categoryProductFilter', selectedCategory);
                EventBus.$emit('hideCommonModal', 'showAllCategories');
            },
        }
    }
</script>