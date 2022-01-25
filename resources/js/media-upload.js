import uuidv4 from './uuidv4'

const mediaUpload = (resourceName, attribute, draftId, onError = () => {}) => {
    const endpoint = `/nova-vendor/laraberg-nova/${resourceName}/attachment/${attribute}`

    return async ({filesList, onFileChange}) => {
        // Start uploading the files
        const promises = Array.from(filesList).map(async (file) => {
            const data = new FormData()
            data.append('Content-Type', file.type)
            data.append('attachment', file)
            data.append('draftId', draftId)

            try {
                const res = await Nova.request().post(endpoint, data)

                return {
                  id: uuidv4(),
                  name: file.name,
                  url: res.data.url
                }
            } catch (e) {
                onError(e)
            }
        })

        const uploadedFiles = await Promise.all(promises)
        onFileChange(uploadedFiles)
    }
}

export default mediaUpload
