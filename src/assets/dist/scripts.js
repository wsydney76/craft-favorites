document.addEventListener('alpine:init', () => {
    Alpine.store('favorites', {
        isInitialized: false,
        isInitializing: false,
        baseUrl: '',
        loggedIn: false,
        message: [],
        showMessage: [],
        timeoutId: '',
        ids: [],

        async initialize(baseUrl = '') {
            if (this.isInitialized || this.isInitializing) {
                return
            }

            this.isInitializing = true
            this.baseUrl = baseUrl

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
            url = this.baseUrl + 'actions/favorites/user-favorites/' + action;
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
            this.showMessagePopup(id, data.message)
        },

        showMessagePopup(id, message) {
            this.showMessage[id] = true
            this.message[id] = message
            if (id) {
                setTimeout(() =>
                        this.showMessage[id] = false
                    , 3000)
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