<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }

        .flying-burger {
            position: fixed;
            font-size: 2rem;
            animation: fly 15s linear infinite;
            opacity: 0.3;
            z-index: -1;
        }

        @keyframes fly {
            0% {
                transform: translate(-100px, -100px) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            100% {
                transform: translate(calc(100vw + 100px), calc(100vh + 100px)) rotate(360deg);
                opacity: 0;
            }
        }

        .error-container {
            text-align: center;
            max-width: 600px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #e74c3c;
            line-height: 1;
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 24px;
            color: #333;
            margin-bottom: 30px;
        }

        .error-description {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .home-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .home-button:hover {
            background-color: #c0392b;
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 80px;
            }
            
            .error-message {
                font-size: 20px;
            }
            
            .error-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div id="burgers-container"></div>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-message">Page introuvable</h1>
        <p class="error-description">
            Désolé, la page que vous recherchez semble avoir disparu dans le vide numérique.
            Elle a peut-être été déplacée ou supprimée.
        </p>
        <a href="/" class="home-button">Retour à l'accueil</a>
    </div>

    <script>
        const fastFoodEmojis = [
            '🍔', // Burger
            '🍟', // Frites
            '🌭', // Hot-dog
            '🍕', // Pizza
            '🌮', // Taco
            '🌯', // Burrito
            '🍗', // Poulet
            '🍖', // Viande
            '🥤', // Boisson
            '🍦', // Glace
            '🍪', // Cookie
            '🍩', // Donut
            '🥐', // Croissant
            '🌶️', // Piment
            '🧀'  // Fromage
        ];

        function createBurger() {
            const food = document.createElement('div');
            food.className = 'flying-burger';
            food.textContent = fastFoodEmojis[Math.floor(Math.random() * fastFoodEmojis.length)];
            food.style.left = Math.random() * window.innerWidth + 'px';
            food.style.top = Math.random() * window.innerHeight + 'px';
            food.style.animationDuration = (Math.random() * 10 + 10) + 's';
            document.getElementById('burgers-container').appendChild(food);
            
            food.addEventListener('animationend', () => {
                food.remove();
            });
        }

        // Créer des éléments toutes les 1.5 secondes
        setInterval(createBurger, 1500);
        // Créer quelques éléments au démarrage
        for(let i = 0; i < 8; i++) {
            setTimeout(createBurger, i * 300);
        }
    </script>
</body>
</html>
