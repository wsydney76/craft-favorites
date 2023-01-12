document.addEventListener('alpine:init', () => {
    Alpine.store('favorites', {
        isInitialized: false,
        isInitializing: false,
        baseUrl: '',
        loggedIn: false,
        message: [],
        showMessage: [],
        ids: [],

        async initialize(baseUrl = '/') {
            // initialize only once per page
            if (this.isInitialized || this.isInitializing) {
                return
            }

            this.isInitializing = true

            // This is the site base url, so that the controller can use the correct language.
            this.baseUrl = baseUrl

            this.doRequest('get')

            // ready
            this.isInitializing = false
            this.isInitialized = true
        },

        async add(id) {
            this.doRequest('add', id)
        },

        async remove(id) {
            this.doRequest('remove', id)
        },

        async doRequest(action, id = 0) {

            // Composer url
            url = this.baseUrl + 'actions/favorites/user-favorites/' + action;
            if (id) {
                url += '?id=' + id
            }

            // We must set this header here
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            })

            // http error code > 200
            if (!response.ok) {
                this.handleError(response)
                return
            }

            // Get the json response
            const data = await response.json()

            // set the data that the alpine component will react to.
            this.loggedIn = data.favorites.loggedIn
            this.ids = data.favorites.ids

            // display message popup
            this.showMessagePopup(id, data.message)
        },

        showMessagePopup(id, message) {

            // let each component have its own message/state
            this.showMessage[id] = true
            this.message[id] = message

            // Hide it after 3 seconds
            setTimeout(() => this.showMessage[id] = false, 3000)

        },

        async handleError(response) {
            const errorText = await response.text();
            if (this.isJson(errorText)) {
                // from exception
                errorData = JSON.parse(errorText)
                alert(`Error! status: ${response.status} message: ${errorData.message}`)
            } else {
                // from ->asFailure($message)
                alert(`Error! status: ${response.status} message: ${errorText}`)
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