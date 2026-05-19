
var activeFilters = { cat_id: "", brand_id: "" };

function addToCart(product_id, stock, isLoggedIn, redirectEncoded) {
    var qtyInput = document.getElementById("qty_input");
    var qty = parseInt(qtyInput ? qtyInput.value : 1, 10);
    var msgEl = document.getElementById("cart_msg");

    if (isNaN(qty) || qty < 1) {
        if (msgEl) msgEl.innerHTML = "Quantity must be at least 1.";
        return;
    }
    if (qty > stock) {
        if (msgEl) msgEl.innerHTML = "Quantity cannot exceed stock (" + stock + ").";
        return;
    }

    if (!isLoggedIn) {
        var redirect = decodeURIComponent(redirectEncoded);
        window.location.href = "../view/login.php?redirect=../view/" + redirect;
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            if (data.error) {
                if (msgEl) msgEl.innerHTML = "Error: " + data.error;
            } else {
                if (msgEl) msgEl.innerHTML = "✔ " + data.message;
                updateCartBadge(data.cart_count);
            }
        }
    };
    xhttp.open("GET", "../control/cart_process.php?action=add&product_id=" + product_id + "&quantity=" + qty, true);
    xhttp.send();
}

function changeQty(cart_id, delta, stock) {
    var qtyEl = document.getElementById("qty_" + cart_id);
    if (!qtyEl) return;
    var current = parseInt(qtyEl.innerHTML, 10);
    var newQty  = current + delta;

    if (newQty < 1) {
        document.getElementById("cart_msg").innerHTML = "Quantity must be at least 1.";
        return;
    }
    if (newQty > stock) {
        document.getElementById("cart_msg").innerHTML = "Cannot exceed stock (" + stock + ").";
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            if (data.error) {
                document.getElementById("cart_msg").innerHTML = data.error;
            } else {
                qtyEl.innerHTML = newQty;
                document.getElementById("row_total_" + cart_id).innerHTML = "$" + data.row_total;
                document.getElementById("grand_total").innerHTML = "$" + data.grand_total;
                updateCartBadge(data.cart_count);
                document.getElementById("cart_msg").innerHTML = "";
            }
        }
    };
    xhttp.open("GET", "../control/cart_process.php?action=update&cart_id=" + cart_id + "&quantity=" + newQty, true);
    xhttp.send();
}

function removeItem(cart_id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            var row  = document.getElementById("cart_row_" + cart_id);
            if (row) row.remove();
            document.getElementById("grand_total").innerHTML = "$" + data.grand_total;
            updateCartBadge(data.cart_count);
            document.getElementById("cart_msg").innerHTML = data.message;
            // If cart is now empty, show empty message
            if (data.cart_count == 0) {
                document.querySelector(".cart-table").outerHTML =
                    "<p style='padding:30px;text-align:center;color:#888;'>Your cart is empty. <a href='product_list.php'>Browse Products</a></p>";
            }
        }
    };
    xhttp.open("GET", "../control/cart_process.php?action=remove&cart_id=" + cart_id, true);
    xhttp.send();
}

function updateCartBadge(count) {
    var badges = document.querySelectorAll("#cart_count");
    badges.forEach(function(el) { el.innerHTML = count; });
}

function setFilter(key, value, el) {
    activeFilters[key] = value;
    var group = (key === "cat_id") ? "cat_id" : "brand_id";
    doSearch();
}

function doSearch() {
    var q         = document.getElementById("searchInput")   ? document.getElementById("searchInput").value.trim() : "";
    var minPrice  = document.getElementById("minPrice")      ? document.getElementById("minPrice").value.trim()   : "";
    var maxPrice  = document.getElementById("maxPrice")      ? document.getElementById("maxPrice").value.trim()   : "";
    var errEl     = document.getElementById("searchError");
    var gridEl    = document.getElementById("product-grid");
    var countEl   = document.getElementById("result-count");

    if (minPrice !== "" && isNaN(parseFloat(minPrice))) {
        errEl.style.display = "block";
        errEl.innerHTML = "Min price must be a number.";
        return;
    }
    if (maxPrice !== "" && isNaN(parseFloat(maxPrice))) {
        errEl.style.display = "block";
        errEl.innerHTML = "Max price must be a number.";
        return;
    }
    if (minPrice !== "" && maxPrice !== "" && parseFloat(minPrice) > parseFloat(maxPrice)) {
        errEl.style.display = "block";
        errEl.innerHTML = "Min price cannot be greater than max price.";
        return;
    }
    errEl.style.display = "none";
    errEl.innerHTML = "";

    var url = SEARCH_URL + "?q=" + encodeURIComponent(q)
            + "&cat_id="   + encodeURIComponent(activeFilters.cat_id)
            + "&brand_id=" + encodeURIComponent(activeFilters.brand_id)
            + "&min_price=" + encodeURIComponent(minPrice)
            + "&max_price=" + encodeURIComponent(maxPrice);

    gridEl.innerHTML = "<p style='color:#aaa;'>Loading...</p>";

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            if (data.error) {
                gridEl.innerHTML = "<p class='err-msg'>" + data.error + "</p>";
                return;
            }
            var products = data.products;
            countEl.innerHTML = products.length + " product(s) found";

            if (products.length === 0) {
                gridEl.innerHTML = "<p style='color:#888;'>No products match your filters.</p>";
                return;
            }

            var html = "";
            for (var i = 0; i < products.length; i++) {
                var p = products[i];
                var review = p.manufacturer_review.length > 75
                           ? p.manufacturer_review.substring(0, 75) + "..."
                           : p.manufacturer_review;
                var imgHtml = p.image_path
                    ? "<img src='../uploads/products/" + p.image_path + "' alt=''>"
                    : "<div class='no-img'>No Image</div>";

                html += "<a href='product_details.php?id=" + p.id + "' class='pcard'>"
                      + "<div class='pcard-img'>" + imgHtml + "</div>"
                      + "<div class='pcard-body'>"
                      + "<div class='pcard-name'>" + p.name + "</div>"
                      + "<div class='pcard-review'>" + review + "</div>"
                      + "<div class='pcard-price'>$" + parseFloat(p.price).toFixed(2) + "</div>"
                      + "</div></a>";
            }
            gridEl.innerHTML = html;
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}

function clearFilters() {
    activeFilters = { cat_id: "", brand_id: "" };
    if (document.getElementById("searchInput"))  document.getElementById("searchInput").value  = "";
    if (document.getElementById("minPrice"))     document.getElementById("minPrice").value     = "";
    if (document.getElementById("maxPrice"))     document.getElementById("maxPrice").value     = "";
    var items = document.querySelectorAll(".filter-item");
    items.forEach(function(el){ el.classList.remove("active-filter"); });
    doSearch();
}
