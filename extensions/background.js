browser.runtime.onMessage.addListener(function(request, sender, sendResponse) {
    if (request.action === 'someAction') {
        fetch('https://api.example.com/data')
            .then(response => response.json())
            .then(data => {
                sendResponse({ data: data });
            })
            .catch(error => {
                console.error(error);
                sendResponse({ error: 'An error occurred' });
            });
        return true;
    }
});
