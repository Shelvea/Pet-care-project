document.addEventListener('DOMContentLoaded', function() {//Vanilla JS fetch version
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const qtyInput = this.previousElementSibling?.querySelector('input.qty'); 
            const qty = qtyInput ? parseInt(qtyInput.value) : 1;

            //makes a POST request to your server-side script add_to_cart.php.
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},//This tells the server:“The body of this request is URL-encoded like a form.”
                body: `id=${productId}&qty=${qty}`//Send this data (id and qty) to the server.
            })//This is how normal HTML <form> data is sent.
            .then(response => response.json())// JSON parsing // takes the raw HTTP response from fetch (which is not yet usable in JS) and parses it as JSON, Converts the JSON string into a real JS object.
            //waits for the HTTP response. 
            .then(data => {// data from a server is always plain text (string) when it arrives.//data is the parsed JSON object from PHP.//From JSON string parsed to JavaScript object? Yes
               //.then is a method for Promises in JavaScript.//Each step waits for the previous step to finish.
                if (data.status === 'success') {
                    alert('✅ ' + data.message);
                } else {
                    alert('❌ ' + data.message);
                }
            });
        });
    });
});
