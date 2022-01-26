<template>
    <div class="laraberg-nova" ref="fieldContainer">
        <default-field :full-width-content="true" :field="field" :errors="errors" :show-help-text="showHelpText">
            <template slot="field">
                <input
                    :id="id"
                    :class="errorClasses"
                    :placeholder="field.name"
                    @change="handleChange"
                    ref="input"
                    type="hidden"
                />
            </template>
        </default-field>
    </div>
</template>

<script>
import {FormField, HandlesValidationErrors} from 'laravel-nova'
import mediaUpload from '../media-upload'
import uuidv4 from '../uuidv4'
import "../../../vendor/van-ons/laraberg/public/js/laraberg"

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],
    data: () => ({
        draftId: uuidv4()
    }),
    computed: {
        id() {
            return this.getId()
        }
    },
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
        getSettings() {
            const settings = {}

            if (this.field.settings?.withFiles) {
                settings.mediaUpload = mediaUpload(
                    this.resourceName,
                    this.field.attribute,
                    this.draftId,
                    (e) => {
                        this.$toasted.show(
                            this.__('An error occured while uploading your file.'),
                            { type: 'error' }
                        )
                    }
                )
            }

            if (this.field.settings?.height) {
                settings.height = `${this.field.settings.height}px`
                delete this.field.settings.height
            }

            return {...settings, ...this.field.settings}
        },

        handleChange(event) {
            this.value = event.target.value
        },
        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
            formData.append(this.field.attribute + 'DraftId', this.draftId)
        },
    },
    mounted() {
        this.$refs.input.value = this.value
        Laraberg.init(this.getId(), this.getSettings())

        /**
         * Temporary fix for Typography buttons submitting the resource
         */
        this.$refs.fieldContainer.addEventListener('click', (e) => {
            const selector = '[data-wp-component="ToggleGroupControlOption"]'
            if (
                e.target.matches(selector)
                || e.target.parentNode.matches(selector)
            ) {
                e.preventDefault()
            }
        })
    },
    beforeDestroy() {
        Laraberg.removeEditor(this.$refs.input)
    }
}
</script>
