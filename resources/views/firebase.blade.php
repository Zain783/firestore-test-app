<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firebase CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Firebase CRUD Operations</h1>
        
        <!-- Documentation Section -->
        <div class="bg-blue-50 p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4 text-blue-800">Omar Arshad Firebase Docs for SQL Developers</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-blue-700">Key Differences from SQL:</h3>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Firebase is a NoSQL database, unlike SQL which is relational</li>
                        <li>Data is stored in collections (similar to tables) and documents (similar to rows)</li>
                        <li>No need to define schema upfront - documents can have different fields</li>
                        <li>Real-time updates are built-in, unlike SQL which requires polling</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-700">How Data is Stored:</h3>
                    <pre class="bg-white p-4 rounded-md text-sm">
Collection: "items"
└── Document 1
    ├── name: "Item 1"
    ├── description: "Description 1"
    └── createdAt: timestamp
└── Document 2
    ├── name: "Item 2"
    ├── description: "Description 2"
    └── createdAt: timestamp</pre>
                </div>
            </div>
        </div>
        
        <!-- Add Item Form -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">Add New Item</h2>
            <form id="addItemForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="itemName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="itemDescription" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" rows="3"></textarea>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">Add Item</button>
            </form>
        </div>

        <!-- Items List -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Items List</h2>
            <div id="itemsList" class="space-y-4">
                <!-- Items will be displayed here -->
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2 class="text-xl font-semibold mb-4">Edit Item</h2>
            <form id="editItemForm" class="space-y-4">
                <input type="hidden" id="editItemId">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="editItemName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="editItemDescription" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" rows="3"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-200">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Firebase Configuration -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-app.js";
        import { getFirestore, collection, addDoc, getDocs, deleteDoc, doc, updateDoc } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-firestore.js";

        const firebaseConfig = {
            apiKey: "AIzaSyARnnvgJH4l_Ssdq62tJlEhzg4g1nd7d9s",
            authDomain: "firestore-test-f0e5a.firebaseapp.com",
            projectId: "firestore-test-f0e5a",
            storageBucket: "firestore-test-f0e5a.firebasestorage.app",
            messagingSenderId: "1082544589294",
            appId: "1:1082544589294:web:0fa985e40b48e2ae2926ff"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);

        // Add Item
        document.getElementById('addItemForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('itemName').value;
            const description = document.getElementById('itemDescription').value;

            try {
                await addDoc(collection(db, "items"), {
                    name: name,
                    description: description,
                    createdAt: new Date()
                });
                document.getElementById('itemName').value = '';
                document.getElementById('itemDescription').value = '';
                loadItems();
            } catch (error) {
                console.error("Error adding document: ", error);
                alert("Error adding item. Please try again.");
            }
        });

        // Load Items
        async function loadItems() {
            const itemsList = document.getElementById('itemsList');
            itemsList.innerHTML = '';
            
            try {
                const querySnapshot = await getDocs(collection(db, "items"));
                querySnapshot.forEach((doc) => {
                    const item = doc.data();
                    const div = document.createElement('div');
                    div.className = 'border p-4 rounded-md hover:shadow-md transition duration-200';
                    div.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg">${item.name}</h3>
                                <p class="text-gray-600 mt-1">${item.description}</p>
                                <p class="text-sm text-gray-500 mt-2">Created: ${item.createdAt?.toDate().toLocaleString() || 'N/A'}</p>
                            </div>
                            <div class="space-x-2">
                                <button onclick="openEditModal('${doc.id}', '${item.name}', '${item.description}')" 
                                        class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition duration-200">
                                    Edit
                                </button>
                                <button onclick="deleteItem('${doc.id}')" 
                                        class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition duration-200">
                                    Delete
                                </button>
                            </div>
                        </div>
                    `;
                    itemsList.appendChild(div);
                });
            } catch (error) {
                console.error("Error getting documents: ", error);
                alert("Error loading items. Please refresh the page.");
            }
        }

        // Delete Item
        window.deleteItem = async (docId) => {
            if (confirm('Are you sure you want to delete this item?')) {
                try {
                    await deleteDoc(doc(db, "items", docId));
                    loadItems();
                } catch (error) {
                    console.error("Error deleting document: ", error);
                    alert("Error deleting item. Please try again.");
                }
            }
        };

        // Modal Functions
        window.openEditModal = (docId, name, description) => {
            document.getElementById('editItemId').value = docId;
            document.getElementById('editItemName').value = name;
            document.getElementById('editItemDescription').value = description;
            document.getElementById('editModal').style.display = 'block';
        };

        window.closeModal = () => {
            document.getElementById('editModal').style.display = 'none';
        };

        // Edit Item Form Submit
        document.getElementById('editItemForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const docId = document.getElementById('editItemId').value;
            const newName = document.getElementById('editItemName').value;
            const newDescription = document.getElementById('editItemDescription').value;

            try {
                await updateDoc(doc(db, "items", docId), {
                    name: newName,
                    description: newDescription
                });
                closeModal();
                loadItems();
            } catch (error) {
                console.error("Error updating document: ", error);
                alert("Error updating item. Please try again.");
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        };

        // Initial load
        loadItems();
    </script>
</body>
</html>