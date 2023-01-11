document.addEventListener('alpine:init', () => {
    Alpine.store('favorites', {
        isInitialized: false,
        isInitializing: false,
        loggedIn: false,
        message: '',
        messageForId: 0,
        ids: [],

        async initialize() {
            if (this.isInitialized || this.isInitializing) {
                return
            }

            this.isInitializing = true

            this.sendActionRequest('get')

            this.isInitializing = false
            this.isInitialized = true
        },

        async add(id) {
            this.sendActionRequest('add', id)
        },

        async remove(id) {
            this.sendActionRequest('remove', id)
        },

        async sendActionRequest(action, id = 0) {
            url = '/actions/favorites/user-favorites/' + action;
            if (id) {
                url += '?id=' + id
            }

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            })

            if (!response.ok) {
                const errorText = await response.text();
                if (this.isJson(errorText)) {
                    errorData = JSON.parse(errorText)
                    alert(`Error! status: ${response.status} message: ${errorData.message}`)
                } else {
                    //report the error
                    alert(`Error! status: ${response.status} message: ${errorText}`)
                }
                return
            }

            const data = await response.json()

            this.loggedIn = data.favorites.loggedIn
            this.ids = data.favorites.ids
            this.message = data.message
            this.messageForId = id
            if(id) {
                setTimeout(() => this.messageForId = 0, 3000)
            }
        },

        isJson(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

    })
})