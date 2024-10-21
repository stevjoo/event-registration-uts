<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->query("SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) AS total_registrations FROM events e");
$events = $stmt->fetchAll();
?>

<script src="https://cdn.tailwindcss.com"></script>
<?php include '../includes/navbar.php'; ?>

<h1 class="text-center text-2xl font-bold my-6">Manage Events</h1>

<div class="grid grid-cols-1 gap-6 px-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($events as $event): ?>
        <div class="border rounded-lg shadow-md p-4 text-left transition-all duration-300 ease-in-out hover:bg-[#2D364C] hover:text-white">
            <img src="../assets/images/<?= $event['banner'] ?>?v=<?= time() ?>" alt="<?= $event['title'] ?> Banner" class="w-full h-48 object-cover rounded-md mb-4">
            <h2 class="text-xl font-semibold mb-2"><?= $event['title'] ?></h2>
            <p class="mb-4"><?= $event['total_registrations'] ?> registrants</p> 
            <button onclick="showDetails(<?= $event['event_id'] ?>)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Details
            </button>
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
        <p id="popup-registrations" class="text-lg text-gray-600 mb-4"></p> 
        
        <div class="flex justify-around">
            <button onclick="editEvent()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit Event
            </button>
            <button onclick="confirmDeleteEvent()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Delete Event
            </button>
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
                document.getElementById('popup-registrations').innerText = `${data[6]} people registered`;
                document.getElementById('event-popup').style.display = 'flex';
            })
            .catch(error => {
                console.error('Error fetching event details:', error);
            });
    }

    function closePopup() {
        document.getElementById('event-popup').style.display = 'none';
    }

    function editEvent() {
        window.location.href = `edit_event.php?event_id=${currentEventId}`;
    }

    function confirmDeleteEvent() {
        const confirmDelete = confirm('Are you sure you want to delete this event? This action cannot be undone.');
        if (confirmDelete) {
            deleteEvent();
        }
    }

    function deleteEvent() {
        fetch(`delete_event.php?event_id=${currentEventId}`, {
            method: 'POST',
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete event');
            }
            return response.text();
        })
        .then(result => {
            alert('Event deleted successfully!');
            location.reload();  
        })
        .catch(error => {
            console.error('Error deleting event:', error);
            alert('An error occurred while trying to delete the event.');
        });
    }
</script>
