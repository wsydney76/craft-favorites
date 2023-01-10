document.addEventListener('alpine:init', () => {
    Alpine.store('favorites', {
        initialized: false,
        initializing: false,
        loggedIn: false,
        ids: [],

        async initialize() {
            if (this.initialized || this.initializing) return

            this.initializing = true

            this.sendActionRequest('get')

            this.initializing = false
            this.initialized = true
        },

        async add(id) {
            this.sendActionRequest('add', id)
        },

        async remove(id) {
            this.sendActionRequest('remove', id)
        },

        async sendActionRequest(action, id=null) {
            url = '/actions/favorites/user-favorites/' + action;
            if (id) {
                url += '?id=' + id
            }

            const response = await fetch(url,{
                headers: {
                    'Accept': 'application/json'
                }
            })

            const data = await response.json()
            this.loggedIn = data.loggedIn
            this.ids = data.ids
            if (data.message) {
                alert(data.message)
            }
        }

    })
})