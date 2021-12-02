const mediaUpload = (resourceName, attribute, draftId, onError = () => {}) => {
    const endpoint = `/nova-vendor/laraberg-nova/${resourceName}/attachment/${attribute}`

    return async ({filesList, onFileChange}) => {
        // Call onFileChange with an array of object URLs to trigger the loading animation
        const files = Array.from(filesList).map(f => ({ url: window.URL.createObjectURL(f) }))
        onFileChange(files)

        // Start uploading the files
        const promises = Array.from(filesList).map(async (file) => {
            const data = new FormData()
            data.append('Content-Type', file.type)
            data.append('attachment', file)
            data.append('draftId', draftId)

            try {
                const res = await Nova.request().post(endpoint, data)
                return { url: res.data.url }
            } catch (e) {
                onError(e)
            }
        })

        onFileChange(await Promise.all(promises))
    }
}

export default mediaUpload
