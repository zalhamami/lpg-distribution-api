<style type="text/css">
    .client-card {
        padding: 1.5rem;
    }
    .client-card .title {
        font-size: 20px;
    }
    .client-card p {
        font-size: 15px;
        line-height: 1.6;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clients') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="mt-3 p-6 bg-white border-b border-gray-200">
                    <div>
                        <x-label for="name">Name</x-label>
                        <x-input type="text" name="name" id="in-client-name" placeholder="Client Name"></x-input>
                    </div>
                    <div class="mt-2">
                        <x-label for="redirect">Redirect</x-label>
                        <x-input type="text" name="redirect" id="in-client-redirect" placeholder="https://my-url.com/callback"></x-input>
                    </div>
                    <div class="mt-3">
                        <x-button onclick="createClient()">Create Client</x-button>
                    </div>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <p>Here are a list of your clients:</p>
                    <div id="card-container"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script type="text/javascript">
    const requestUrl = '{{ url('oauth/clients') }}'
    setTimeout(() => {
        getAllClients()
    }, 500)

    function getAllClients() {
        axios.get(requestUrl)
            .then((res) => {
                const data = res.data
                const items = []
                data.forEach((e) => {
                    items.push(`
                        <div class="client-card shadow-md mt-2">
                            <h3 class="title mb-2">${e.name}</h3>
                            <p><b>Client ID: </b>${e.id}</p>
                            <p><b>Client Secret: </b>${e.secret}</p>
                            <p><b>Client Redirect: </b>${e.redirect}</p>
                            <div class="mt-4">
                                <button class="btn btn-red" onclick="deleteClient('${e.id}')">Delete</button>
                            </div>
                        </div>
                    `)
                })
                const container = document.getElementById('card-container')
                container.innerHTML = items
            })
            .catch((err) => {
                console.log(err)
            })
    }

    function createClient() {
        const name = document.getElementById('in-client-name')
        const redirect = document.getElementById('in-client-redirect')
        axios.post(requestUrl, {
            name: name.value,
            redirect: redirect.value
        }).then((res) => {
            getAllClients()
            name.value = ''
            redirect.value = ''
        })
        .catch((err) => {
            console.log(err)
        })
    }

    function deleteClient(id) {
        const requestUrl = '{{ url('oauth/clients') }}'
        axios.delete(`${requestUrl}/${id.trim()}`)
            .then((res) => {
                getAllClients()
            })
            .catch((err) => {
                console.log(err)
            })
    }
</script>
