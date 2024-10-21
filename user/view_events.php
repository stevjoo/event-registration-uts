<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'user') {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll();
?>

<?php include '../includes/navbar.php'; ?>

<h1 class="text-center text-2xl font-bold my-6">Events Happening</h1>

<div class="grid grid-cols-1 gap-6 px-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($events as $event): ?>
        <div class="border rounded-lg shadow-md p-4 text-left transition-all duration-300 hover:bg-[#2D364C] hover:shadow-xl hover:text-white">
            <img src="../assets/images/<?= $event['banner'] ?>?v=<?= time() ?>" alt="<?= $event['title'] ?> Banner" class="w-full h-48 object-cover rounded-md mb-4 shadow-md">
            <h2 class="text-xl font-semibold mb-2"><?= $event['title'] ?></h2>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="showDetails(<?= $event['event_id'] ?>)">Details</button>
        </div>
    <?php endforeach; ?>
</div>

<div id="event-popup" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center" style="display:none;">
    <div class="relative bg-white p-12 rounded-lg shadow-lg w-11/12 max-w-lg text-center">

        <button class="absolute top-1 right-1 opacity-50 hover:opacity-100 text-xl font-bold px-3 py-3 rounded-full" onclick="closePopup()">
            <svg width="20px" height="20px" viewBox="-0.5 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 21.32L21 3.32001" stroke="#ff0000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M3 3.32001L21 21.32" stroke="#ff0000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
        </button>

        <div class="rounded-md border border-slate-300 border-1 p-2 shadow-md mb-4">
            <img id="popup-image" src="" alt="Event Image" class="w-full h-48 object-cover rounded-md">
        </div>        
        <h3 id="popup-title" class="text-3xl font-semibold mb-2"></h3>
        <p id="popup-description" class="text-lg font-normal mb-2"></p>
        <p id="popup-date-time" class="text-lg font-normal mb-2"></p>
        <p id="popup-location" class="text-lg font-normal mb-2"></p>
        
        <div class="flex justify-around mt-6">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="registerEvent(<?= $event['event_id'] ?>)">Register</button>
        </div>
    </div>
</div>

<script>
    let currentEventId; 

    function showDetails(eventId) {
        currentEventId = eventId; 

        fetch(`get_event_details.php?event_id=${eventId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(event => {
                const data = event.split('|'); 
                document.getElementById('popup-title').innerText = data[0]; 
                document.getElementById('popup-image').src = '../assets/images/' + data[1] + '?v=' + new Date().getTime();
                document.getElementById('popup-description').innerText = data[2]; 
                document.getElementById('popup-date-time').innerText = `Date: ${data[3]}, Time: ${data[4]}`; 
                document.getElementById('popup-location').innerText = `Location: ${data[5]}`;
                document.getElementById('event-popup').style.display = 'flex';
            })
            .catch(error => {
                console.error('Error fetching event details:', error);
            });
    }

    function closePopup() {
        document.getElementById('event-popup').style.display = 'none';
    }

    function registerEvent() {
        const userId = <?= $_SESSION['user_id'] ?>; 

        fetch('register_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `event_id=${currentEventId}&user_id=${userId}` 
        })
        .then(response => {
            return response.json(); 
        })
        .then(data => {
            if (data.status === 'success') {
                alert('You have successfully registered for the event!');
                closePopup(); 
            } else {
                alert(`Registration failed: ${data.message}`); 
            }
        })
        .catch(error => {
            console.error('Error registering for event:', error);
            alert('There was an error registering for the event. Please try again.');
        });
    }

</script>

<!-- <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        margin: 20px 0;
    }

    .event-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        padding: 0 10px;
    }

    .event-banner {
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        width: 200px;
        flex: 1 1 calc(25% - 20px);
        max-width: 200px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .event-banner img {
        max-width: 100%;
        border-radius: 8px;
    }

    .event-banner button {
        margin-top: 10px;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        cursor: pointer;
    }

    .event-banner button:hover {
        background-color: #0056b3;
    }

    .popup {
        position: fixed;
        z-index: 999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .popup-content {
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        position: relative;
        width: 90%;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    .popup-image {
        max-width: 80%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .popup-buttons {
        margin-top: 15px;
    }

    .popup-buttons button {
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        cursor: pointer;
        display: block;
        margin: 5px auto;
        width: 80%;
    }

    .popup-buttons button:nth-child(2) {
        background-color: #dc3545; 
    }

    .popup-buttons button:nth-child(2):hover {
        background-color: #c82333;
    }

    .popup-buttons button:hover {
        background-color: #0056b3;
    }

    /* Media Queries for Responsiveness */
    @media (max-width: 768px) {
        .event-banner {
            flex: 1 1 calc(50% - 20px);
        }
    }

    @media (max-width: 480px) {
        .event-banner {
            flex: 1 1 100%;
            max-width: 100%;
        }

        .popup-content {
            width: 90%;
            max-width: 90%;
        }
    }
</style> -->
