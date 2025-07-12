const addToCartButtons = document.querySelectorAll(".add-to-cart");
const orderList = document.getElementById("order-list");
const totalPriceDisplay = document
  .getElementById("order-total")
  .querySelector("p");
const checkoutButton = document.getElementById("checkout-button");
const tabLinks = document.querySelectorAll(".tab-link");
const burgerItems = document.querySelectorAll(".burger-item");

// Modale
const customizeModal = document.getElementById("customize-modal");
const customizeForm = document.getElementById("customize-form");
const ingredientsContainer = document.getElementById("ingredients-options");

let selectedBurgerName = "";
let selectedBurgerPrice = 0;

let totalPrice = 0;
let orderItems = [];

// Onglets
tabLinks.forEach((tab) => {
  tab.addEventListener("click", () => {
    const category = tab.getAttribute("data-category");
    tabLinks.forEach((link) => link.classList.remove("active"));
    tab.classList.add("active");

    burgerItems.forEach((item) => {
      if (
        category === "all" ||
        item.getAttribute("data-category") === category
      ) {
        item.style.display = "block";
      } else {
        item.style.display = "none";
      }
    });
  });
});

addToCartButtons.forEach((button) => {
  button.addEventListener("click", () => {
    let burgerName = button.getAttribute("data-burger");
    let burgerPrice = parseFloat(button.getAttribute("data-price"));

    let selectedSauce = "";
    let saucePrice = 0;
    const isFries = [
      "Frites Maison",
      "Frites de Patate Douce",
      "Cheesy Fries",
      "Loaded Fries",
    ].includes(burgerName);

    if (burgerName === "Frites Maison") {
      selectedSauce = document.getElementById("sauce-fries-maison").value;
      burgerName += ` avec sauce ${selectedSauce}`;
      saucePrice = getSaucePrice(selectedSauce);
      burgerPrice += saucePrice;
    } else if (burgerName === "Frites de Patate Douce") {
      selectedSauce = document.getElementById("sauce-patate-douce").value;
      burgerName += ` avec sauce ${selectedSauce}`;
      saucePrice = getSaucePrice(selectedSauce);
      burgerPrice += saucePrice;
    } else if (burgerName === "Cheesy Fries") {
      selectedSauce = document.getElementById("sauce-cheesy").value;
      burgerName += ` avec sauce ${selectedSauce}`;
      saucePrice = getSaucePrice(selectedSauce);
      burgerPrice += saucePrice;
    } else if (burgerName === "Loaded Fries") {
      selectedSauce = document.getElementById("sauce-loaded").value;
      burgerName += ` avec sauce ${selectedSauce}`;
      saucePrice = getSaucePrice(selectedSauce);
      burgerPrice += saucePrice;
    }

    if (isFries) {
      const existingItem = orderItems.find((item) => item.name === burgerName);
      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        orderItems.push({ name: burgerName, price: burgerPrice, quantity: 1 });
      }

      totalPrice += burgerPrice;
      updateOrderSummary();
    } else {
      // Personnalisation via modale
      selectedBurgerName = burgerName;
      selectedBurgerPrice = burgerPrice;

      const ingredients = getIngredientsByBurger(burgerName);
      if (ingredientsContainer) {
        ingredientsContainer.innerHTML = "";

        ingredients.forEach((ing) => {
          const label = document.createElement("label");
          const checkbox = document.createElement("input");
          checkbox.type = "checkbox";
          checkbox.name = "ingredients";
          checkbox.value = ing;

          label.appendChild(checkbox);
          label.appendChild(document.createTextNode(" " + ing));
          ingredientsContainer.appendChild(label);
        });

        if (customizeModal) {
          customizeModal.style.display = "flex";
        }
      }
    }
  });
});

function getSaucePrice(sauce) {
  switch (sauce) {
    case "mayonnaise":
    case "ketchup":
    case "smokey":
    case "curry":
    case "garlic":
    case "sweet-chilli":
    case "truffle":
      return 1;
    case "bbq":
      return 1.5;
    case "swiss":
      return 1.2;
    default:
      return 0;
  }
}

function getIngredientsByBurger(name) {
  const base = {
    "O.G. OKLAHOMA": ["oignons", "cheddar", "pickles", "salade", "sauce O.G."],
    SMASHIC: ["oignons rouges", "pickles", "ketchup", "moutarde"],
    TRUFFLE: ["cheddar", "salade", "oignons frits", "sauce truffe"],
    SMOKEY: [
      "cheddar",
      "bacon de boeuf",
      "salade",
      "oignons frits",
      "sauce fumée",
    ],
    "DAE LOVERS": ["poulet frit", "cheddar", "coleslaw", "sauce Gochujang"],
    SOSS: [
      "cheddar",
      "bacon de boeuf",
      "onion rings",
      "salade",
      "oignons frits",
      "sauce BBQ",
    ],
  };

  return base[name] || [];
}

function updateOrderSummary() {
  orderList.innerHTML = "";

  if (orderItems.length === 0) {
    orderList.innerHTML = "<p>Aucun article dans votre panier.</p>";
    totalPriceDisplay.textContent = "Total : 0.00€";
    checkoutButton.disabled = true;
  } else {
    orderItems.forEach((item) => {
      const li = document.createElement("div");
      li.classList.add("order-item");
      li.innerHTML = `${item.name} x${item.quantity} - ${(
        item.price * item.quantity
      ).toFixed(2)}€ 
                <button class="delete-item">Retirer</button>`;
      orderList.appendChild(li);

      li.querySelector(".delete-item").addEventListener("click", () => {
        removeItem(item);
      });
    });

    totalPriceDisplay.textContent = `Total : ${totalPrice.toFixed(2)}€`;
    checkoutButton.disabled = false;
  }
}

function removeItem(itemToRemove) {
  const foundItem = orderItems.find((item) => item.name === itemToRemove.name);
  if (foundItem) {
    foundItem.quantity -= 1;
    totalPrice -= foundItem.price;

    if (foundItem.quantity === 0) {
      orderItems = orderItems.filter((item) => item !== foundItem);
    }

    updateOrderSummary();
  }
}

if (customizeForm) {
  customizeForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Récupérer les ingrédients retirés
    const removed = Array.from(
      document.querySelectorAll('input[name="ingredients"]:checked')
    ).map((cb) => cb.value);

    // Récupérer les suppléments ajoutés
    const extras = Array.from(
      document.querySelectorAll(
        '#extras-options input[type="checkbox"]:checked'
      )
    );
    const extrasText = extras.map((extra) => extra.value);
    const extrasPrice = extras.reduce(
      (total, extra) => total + parseFloat(extra.dataset.price),
      0
    );

    // Nom final du burger
    let finalName = selectedBurgerName;
    if (removed.length > 0) {
      finalName += " (" + removed.map((i) => "sans " + i).join(", ") + ")";
    }
    if (extrasText.length > 0) {
      finalName += " (avec " + extrasText.join(", ") + ")";
    }

    // Prix final avec suppléments
    const finalPrice = selectedBurgerPrice + extrasPrice;

    // Ajouter ou mettre à jour l'article dans le panier
    const existingItem = orderItems.find((item) => item.name === finalName);
    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      orderItems.push({ name: finalName, price: finalPrice, quantity: 1 });
    }

    // Mettre à jour le prix total
    totalPrice += finalPrice;
    updateOrderSummary();

    // Fermer la modale
    if (customizeModal) {
      customizeModal.style.display = "none";
    }
  });
}

// Fermer la modale si clic à l’extérieur
if (customizeModal) {
  window.addEventListener("click", function (e) {
    if (e.target === customizeModal) {
      customizeModal.style.display = "none";
    }
  });
}

// Fermer via bouton "Annuler"
const cancelBtn = document.getElementById("cancel-customization");
if (cancelBtn) {
  cancelBtn.addEventListener("click", () => {
    if (customizeModal) {
      customizeModal.style.display = "none";
    }
  });
}

// Connexion
function checkLoginStatus() {
  // Utilisation de sessionStorage pour simuler la session de l'utilisateur
  const isLoggedIn = sessionStorage.getItem("loggedIn");

  const loginBtn = document.getElementById("login-btn");
  const profileBtn = document.getElementById("profile-btn");
  const logoutBtn = document.getElementById("logout-btn");

  if (isLoggedIn) {
    loginBtn.style.display = "none"; // Cacher le bouton "Se connecter"
    profileBtn.style.display = "inline-block"; // Afficher le bouton "Mon Profil"
    logoutBtn.style.display = "inline-block"; // Afficher le bouton "Se déconnecter"
  } else {
    loginBtn.style.display = "inline-block"; // Afficher le bouton "Se connecter"
    profileBtn.style.display = "none"; // Cacher le bouton "Mon Profil"
    logoutBtn.style.display = "none"; // Cacher le bouton "Se déconnecter"
  }
}

function logout() {
  sessionStorage.removeItem("loggedIn");
  window.location.href = "index.html";
}

window.onload = checkLoginStatus;

document.getElementById("back-to-cart").addEventListener("click", () => {
  document.getElementById("payment-choice-modal").style.display = "none";
});



document.getElementById("pay-online").addEventListener("click", () => {
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "checkout.php";

  const input = document.createElement("input");
  input.type = "hidden";
  input.name = "cart";
  input.value = JSON.stringify(orderItems);

  form.appendChild(input);
  document.body.appendChild(form);
  form.submit();
});

document.getElementById("pay-counter").addEventListener("click", () => {
  document.getElementById("payment-choice-modal").style.display = "none";
  document.getElementById("pay-counter-form").style.display = "flex";
  document.getElementById("counter-cart-json").value = JSON.stringify(orderItems);
});

window.addEventListener("click", function (e) {
  if (e.target === customizeModal) {
    customizeModal.style.display = "none";
  }
  if (e.target === document.getElementById("payment-choice-modal")) {
    document.getElementById("payment-choice-modal").style.display = "none";
  }
  if (e.target === document.getElementById("pay-counter-form")) {
    document.getElementById("pay-counter-form").style.display = "none";
  }
});

checkoutButton.addEventListener("click", function (e) {
  e.preventDefault();

  if (orderItems.length === 0) {
    alert("Votre panier est vide.");
    return;
  }

  document.getElementById("payment-choice-modal").style.display = "flex";
});

// Retour au choix du paiement
document.getElementById("back-to-choice").addEventListener("click", () => {
  document.getElementById("pay-counter-form").style.display = "none";
  document.getElementById("payment-choice-modal").style.display = "flex";
});

// Envoi de la commande au comptoir
document.getElementById("submit-counter-order").addEventListener("click", () => {
  const email = document.getElementById("counter-email").value;
  const cart = JSON.stringify(orderItems);

  if (!email) {
    alert("Veuillez entrer une adresse email.");
    return;
  }

  const form = document.createElement("form");
  form.method = "POST";
  form.action = "paiement_comptoir.php"; // Change si besoin

  const inputEmail = document.createElement("input");
  inputEmail.type = "hidden";
  inputEmail.name = "email";
  inputEmail.value = email;

  const inputCart = document.createElement("input");
  inputCart.type = "hidden";
  inputCart.name = "cart";
  inputCart.value = cart;

  form.appendChild(inputEmail);
  form.appendChild(inputCart);

  document.body.appendChild(form);
  form.submit();
});
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".burger-item").forEach((item, i) => {
        item.style.animationDelay = `${i * 100}ms`;
    });
});
document.getElementById("theme-toggle").addEventListener("click", () => {
    const html = document.documentElement;
    const current = html.getAttribute("data-theme");
    html.setAttribute("data-theme", current === "dark" ? "light" : "dark");
});
const floatingCartBtn = document.getElementById("floating-cart-btn");
const cartModal = document.getElementById("cart-modal");
const modalCloseBtn = document.getElementById("modal-close");
const modalOrderList = document.getElementById("modal-order-list");
const modalTotalPrice = document.getElementById("modal-total-price");

floatingCartBtn.addEventListener("click", () => {
  updateModalCart();
  cartModal.style.display = "flex";
});

modalCloseBtn.addEventListener("click", () => {
  cartModal.style.display = "none";
});

window.addEventListener("click", (e) => {
  if (e.target === cartModal) {
    cartModal.style.display = "none";
  }
});

function updateModalCart() {
  modalOrderList.innerHTML = "";

  if (orderItems.length === 0) {
    modalOrderList.innerHTML = "<p>Aucun article dans votre panier.</p>";
    modalTotalPrice.textContent = "Total : 0.00€";
    return;
  }

  orderItems.forEach((item) => {
    const div = document.createElement("div");
    div.classList.add("item-line");
    div.innerHTML = `<span>${item.name} x${item.quantity}</span><span>${(item.price * item.quantity).toFixed(2)}€</span>`;
    modalOrderList.appendChild(div);
  });

  modalTotalPrice.textContent = `Total : ${totalPrice.toFixed(2)}€`;
}

document.getElementById("modal-checkout").addEventListener("click", () => {
  cartModal.style.display = "none";
  checkoutButton.click(); // déclenche la suite du processus
});




