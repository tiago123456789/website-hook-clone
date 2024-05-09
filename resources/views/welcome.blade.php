<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Clone Website.hook</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body class="container-fluid mt-2">
    <div class="alert alert-warning" role="alert">
        Your webhook url => {{ $link }}
    </div>

    <h2>Requests:</h2>
    <div class="accordion" id="requests">

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.8/axios.min.js" integrity="sha512-PJa3oQSLWRB7wHZ7GQ/g+qyv6r4mbuhmiDb8BjSFZ8NZ2a42oTtAq5n0ucWAwcQDlikAtkub+tPVCw4np27WCg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        (() => {
            let intervalInSeconds = 5;
            localStorage.setItem("lastRequestAt", new Date().toISOString())

            function addOneRequest(data) {
                const header = JSON.parse(data.header)
                let headerItems = "(empty)"
                const hasHeader = Object.keys(header).length > 0
                if (hasHeader) {
                    headerItems = Object.keys(header).map(key => {
                    return (`
                        <li>${key}: ${ header[key] }</li>
                    `)
                    })
                    headerItems = headerItems.join("")
                }

                const query = JSON.parse(data.query)
                let queryItems = "(empty)"
                const hasQuerystring = Object.keys(query).length > 0
                if (hasQuerystring) {
                    queryItems = Object.keys(query).map(key => {
                        return (`
                            <li>${key}: ${ query[key] }</li>
                        `)
                    })
                    queryItems = queryItems.join("")
                }

                const newItem = `
        <div class="accordion-item">
            <h2 class="accordion-header" id="${data.id}">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Id: ${data.id} | Method: ${ data.method } | Url: ${ data.url }
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body text-left">
                    <div>
                        <h4>Headers:</h4>
                        <ul style="list-style: none;">
                            ${headerItems}
                        </ul>
                    </div>
                    <div>
                        <h4>Querystrings:</h4>
                        <ul style="list-style: none;">
                            ${queryItems}
                        </ul>
                    </div>
                    <div>
                        <h4>Request body:</h4>
                        <code>
                            ${data.body}
                        </code>
                    </div>
                </div>
            </div>
        </div>
        `
                document.getElementById("requests").innerHTML += newItem
            }

            function addNewRequests(data) {
                data.forEach(addOneRequest)
            }

            function getLastRequests() {
                const lastRequestAt = localStorage.getItem("lastRequestAt")
                axios.get(`/webhook/{{ $webhook_id }}/last-requests?lastRequestAt=${lastRequestAt}`)
                    .then(response => {
                        localStorage.setItem("lastRequestAt", new Date().toISOString())
                        addNewRequests(response.data)
                    })
            }

            setInterval(() => {
                getLastRequests()
            }, intervalInSeconds * 1000)
        })()
    </script>
</body>

</html>