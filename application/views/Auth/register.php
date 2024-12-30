
<?= form_open('auth/register', ['class' => 'space-y-6']) ?>
    <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md" 
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md" 
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md" 
               required>
    </div>

    <button type="submit" 
            class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
        Register
    </button>
<?= form_close() ?>