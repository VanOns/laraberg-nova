<template>
    <div class="laraberg-nova">
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
            const settingKeys = [
                'disabledCoreBlocks',
                'alignWide',
                'supportsLayout',
                'maxWidth',
                'imageEditing',
                'colors',
                'gradients',
                'fontSizes'
            ]

            if (this.field.withFiles) {
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

            if (this.field.height) {
                settings.height = `${this.field.height}px`
            }

            settingKeys.forEach(key => {
                if (!Object.keys(this.field).includes(key)) {
                    return;
                }

                settings[key] = this.field[key]
            })

            return settings
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
    }
}
</script>
