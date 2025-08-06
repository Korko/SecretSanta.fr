<?php

return [
    // General
    'app_name' => 'Secret Santa',
    'welcome' => 'Welcome to Secret Santa',
    'loading' => 'Loading...',
    'error' => 'An error occurred',
    'success' => 'Operation successful',
    'confirm' => 'Are you sure?',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'create' => 'Create',
    'update' => 'Update',
    'back' => 'Back',
    'next' => 'Next',
    'previous' => 'Previous',
    'yes' => 'Yes',
    'no' => 'No',

    // Authentication
    'login' => 'Login',
    'logout' => 'Logout',
    'register' => 'Register',
    'email' => 'Email',
    'password' => 'Password',
    'password_confirmation' => 'Confirm Password',
    'remember_me' => 'Remember me',
    'forgot_password' => 'Forgot your password?',
    'reset_password' => 'Reset Password',

    // Draw Management
    'draw' => [
        'title' => 'Draw Title',
        'description' => 'Description',
        'event_date' => 'Event Date',
        'budget' => 'Budget',
        'create' => 'Create Draw',
        'update' => 'Update Draw',
        'delete' => 'Delete Draw',
        'launch' => 'Launch Draw',
        'status' => [
            'draft' => 'Draft',
            'ready' => 'Ready',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ],
        'participants_count' => ':count participant(s)',
        'no_participants' => 'No participants yet',
        'add_participant' => 'Add Participant',
        'remove_participant' => 'Remove Participant',
        'exclusions' => 'exclusions',
        'add_exclusion' => 'Add Exclusion Rule',
    ],

    // Participants
    'participant' => [
        'name' => 'Name',
        'email' => 'Email',
        'status' => 'Status',
        'joined_at' => 'Joined',
        'invite' => 'Invite Participant',
        'remove' => 'Remove Participant',
        'access_key' => 'Access Key',
        'view_assignment' => 'View Assignment',
        'your_target' => 'You are Secret Santa for',
        'status_types' => [
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
        ],
    ],

    // Messages
    'message' => [
        'send' => 'Send Message',
        'reply' => 'Reply',
        'anonymous' => 'Anonymous Message',
        'from_santa' => 'From your Secret Santa',
        'to_recipient' => 'To your recipient',
        'placeholder' => 'Type your message...',
        'sent' => 'Message sent successfully',
        'failed' => 'Failed to send message',
        'types' => [
            'question' => 'Question',
            'thank_you' => 'Thank You',
            'hint' => 'Hint',
            'general' => 'General',
        ],
    ],

    // Validation Messages
    'validation' => [
        'required' => 'This field is required',
        'email' => 'Please enter a valid email address',
        'min' => 'Must be at least :min characters',
        'max' => 'Must not exceed :max characters',
        'confirmed' => 'The confirmation does not match',
        'unique' => 'This value is already taken',
        'date' => 'Please enter a valid date',
        'after' => 'Must be a date after :date',
    ],

    // Error Messages
    'errors' => [
        'not_found' => 'Resource not found',
        'unauthorized' => 'You are not authorized to perform this action',
        'forbidden' => 'Access forbidden',
        'server_error' => 'An internal server error occurred',
        'draw_failed' => 'Unable to complete the draw. Please check exclusion rules.',
        'invalid_key' => 'Invalid access key',
        'expired_link' => 'This link has expired',
    ],

    // Success Messages
    'success_messages' => [
        'draw_created' => 'Draw created successfully',
        'draw_updated' => 'Draw updated successfully',
        'draw_deleted' => 'Draw deleted successfully',
        'draw_launched' => 'Draw launched successfully',
        'participant_added' => 'Participant added successfully',
        'participant_removed' => 'Participant removed successfully',
        'message_sent' => 'Message sent successfully',
        'profile_updated' => 'Profile updated successfully',
    ],
];
