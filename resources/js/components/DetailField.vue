<template>
    <panel-item :field="field">
        <template slot="value">
            <div class="editor-styles-wrapper" v-if="expanded" v-html="field.value"/>
            <a
                @click="toggle"
                class="cursor-pointer dim inline-block text-primary font-bold"
                :class="{ 'mt-6': expanded }"
                role="button"
            >
                {{ showHideLabel }}
            </a>

        </template>
    </panel-item>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],
    data: () => ({
        expanded: false
    }),
    methods: {
        toggle() {
            this.expanded = !this.expanded
        }
    },
    computed: {
        showHideLabel() {
            return this.expanded ? this.__('Hide Content') : this.__('Show Content')
        }
    }
}
</script>
