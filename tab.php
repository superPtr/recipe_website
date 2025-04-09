<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Tab</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            width: 100%;
            height: 100vh;
            margin: 0;
            padding: 0;
            display: grid;
            place-items: top; /*center*/
            background: #000000;
        }

        .container {
            width: 1000px;
            height: 300px;
            display: flex;
            position: relative;
        }

        input {
            display: none;
        }

        .tab-name {
            color: #ffffff;
            font-family: poppins;
            height: 45px;
            background: rgba(255, 255, 255, 0.25);
            padding: 10px 30px;
            box-sizing: border-box;
            cursor: pointer;
            transition: 0.25s;
        }

        input:checked + label .tab-name {
            background: rgb(243, 240, 58);
            color: #000000;
            font-size: 18px;
            font-weight: 800;
            border-top-left-radius: 5px;
            border-top-right-radiues: 5px;
        }

        .tab-content {
            position: absolute;
            top: 50px;
            left: 0;
            background: #ffffff;
            color: #000000;
            font-family: poppins;
            padding: 15px;
            box-sizing: border-box;
            border-radius: 5px;
            opacity: 0;
            z-index: 0;
            transition: 0.5s;
        }

        input:checked + label .tab-content {
            opacity: 1;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <input type="radio" name="option" id="1"  checked/>
        <label for="1">
            <div class="tab-name">Home</div>
            <div class="tab-content">
                <!-- grid -->
                <div class="wrapper">
                    <div class="box">
                        <i class='bx bx-baguette'></i>
                        <h2>Web Development</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
                        <a href="#" class="btn">Read More</a>
                    </div>

                    <div class="box">
                        <i class='bx bxs-paint' ></i>
                        <h2>UI/UX Design</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
                        <a href="#" class="btn">Read More</a>
                    </div>

                    <div class="box">
                        <i class='bx bxs-paint' ></i>
                        <h2>UI/UX Design</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
                        <a href="#" class="btn">Read More</a>
                    </div>

                    <div class="box">
                        <i class='bx bxs-paint' ></i>
                        <h2>UI/UX Design</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
                        <a href="#" class="btn">Read More</a>
                    </div>
                </div>

            
            </div>
        </label>

        <input type="radio" name="option" id="2"  checked/>
        <label for="2">
            <div class="tab-name">Shop</div>
            <div class="tab-content">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
        </label>

        <input type="radio" name="option" id="3"  checked/>
        <label for="3">
            <div class="tab-name">report</div>
            <div class="tab-content">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
        </label>
    </div>
</body>
</html>