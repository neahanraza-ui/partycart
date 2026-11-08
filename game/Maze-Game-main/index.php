<?php
session_start();
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maze Challenge - LUXORA Rewards</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div id="page">
        <!-- Intro Modal -->
        <div id="Intro-Modal" class="modal-overlay visible">
            <div class="modal-content">
                <h2>Maze Challenge</h2>
                <p>Navigate the maze and reach the house to unlock exclusive LUXORA discounts!</p>
                
                <div class="discount-info">
                    <div class="tier"><strong>Easy</strong> 5% OFF</div>
                    <div class="tier"><strong>Medium</strong> 7% OFF</div>
                    <div class="tier"><strong>Hard</strong> 15% OFF (2 Uses!)</div>
                    <div class="tier"><strong>Extreme</strong> 20% OFF (2 Uses!)</div>
                </div>
                
                <p style="font-size: 14px;"><i>Note: One attempt per email. Coupons are bound to your email.</i></p>
                
                <input id="startBtn" type="button" onclick="closeModal('Intro-Modal')" value="I'm Ready!" style="width: 100%; background: var(--gold-primary); color: #000; font-weight: 700;">
            </div>
        </div>

        <!-- Victory Modal -->
        <div id="Victory-Modal" class="modal-overlay">
            <div class="modal-content">
                <h2 id="victoryTitle">Congratulations!</h2>
                <p id="victoryBody">You've successfully mastered the maze.</p>
                
                <div id="rewardSection">
                    <p style="color: var(--gold-primary); font-weight: 700; font-size: 20px;">Your Reward: <span id="rewardAmount">--</span></p>
                    
                    <div class="email-input-group">
                        <label for="claimEmail">Enter Email to Claim Coupon:</label>
                        <input type="email" id="claimEmail" value="<?php echo htmlspecialchars($user_email); ?>" placeholder="your@email.com">
                    </div>
                    
                    <button class="btn-claim" onclick="claimCoupon()">Claim Coupon</button>
                </div>

                <div id="claimSuccess" style="display: none;">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: var(--gold-primary); margin-bottom: 15px;"></i>
                    <p style="font-size: 18px; color: var(--gold-primary);">Coupon Claimed!</p>
                    <p>Your code: <strong id="couponCodeDisplay" style="font-size: 24px; letter-spacing: 2px;">---</strong></p>
                    <p style="font-size: 13px;">Copy this code and use it at checkout.</p>
                    <button class="btn-claim" onclick="location.reload()">Play Again</button>
                    <button class="btn-claim" style="background: transparent; border: 1px solid var(--gold-primary); color: var(--gold-primary);" onclick="window.location.href='../../checkout.php'">Go to Checkout</button>
                </div>
            </div>
        </div>

        <h1>LUXORA Maze</h1>

        <div id="menu">
            <div class="custom-select">
                <select id="diffSelect">
                    <option value="10">Easy (5%)</option>
                    <option value="15">Medium (7%)</option>
                    <option value="25">Hard (15%)</option>
                    <option value="38">Extreme (20%)</option>                                      
                </select>
            </div>
            <input id="startMazeBtn" type="button" onclick="makeMaze()" value="Start Game" />
        </div>

        <div id="view">
            <div id="mazeContainer">
                <canvas id="mazeCanvas" height="1100" width="1100"></canvas>
            </div>
        </div>

        <p id="instructions"><i class="fas fa-keyboard"></i> Use arrow keys to move the key to the house!</p>
    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.touchswipe/1.6.18/jquery.touchSwipe.min.js"></script>
    <script src="./script.js"></script>
</body>
</html>
