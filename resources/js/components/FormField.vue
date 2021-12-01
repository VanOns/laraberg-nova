<template>
    <div class="laraberg-nova">
        <default-field :full-width-content="true" :field="field" :errors="errors" :show-help-text="showHelpText">
            <template slot="field">
                <input
                    :id="getId()"
                    :class="errorClasses"
                    :placeholder="field.name"
                    v-model="value"
                    type="text"
                />
            </template>
        </default-field>
    </div>
</template>

<script>
import {FormField, HandlesValidationErrors} from 'laravel-nova'
import "../../../vendor/van-ons/laraberg/public/js/laraberg"

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        getId() {
            return `${this.field.component}--${this.field.attribute}`
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },
    },
    mounted() {
        Laraberg.init(this.getId())
    },
}
</script>
