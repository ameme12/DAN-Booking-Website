INSERT INTO User (userId, firstName, lastName)
VALUES
    (1, 'Alice', 'Smith'),
    (2, 'Bob', 'Johnson'),
    (3, 'Charlie', 'Brown'),
    (4, 'Diana', 'Prince'),
    (5, 'Ethan', 'Hunt'),
    (6, 'Fiona', 'Harper'),
    (7, 'George', 'Clooney'),
    (8, 'Hannah', 'Montana'),
    (9, 'Ian', 'Sommer'),
    (10, 'Jack', 'Ryan'),
    (11, 'Karen', 'Page'),
    (12, 'Leo', 'Messi'),
    (13, 'Martha', 'Stewart'),
    (14, 'Nathan', 'Drake'),
    (15, 'Olivia', 'Benson'),
    (16, 'Paul', 'Rudd'),
    (17, 'Quincy', 'Adams'),
    (18, 'Rachel', 'Green'),
    (19, 'Steve', 'Rogers'),
    (20, 'Tony', 'Stark'),
    (21, 'Uma', 'Thurman'),
    (22, 'Victor', 'Hugo'),
    (23, 'Will', 'Smith'),
    (24, 'Xander', 'Cage'),
    (25, 'Yasmine', 'Bleeth'),
    (26, 'Zack', 'Efron'),
    (27, 'Liam', 'Hemsworth'),
    (28, 'Emma', 'Watson'),
    (29, 'Chris', 'Evans'),
    (30, 'Scarlett', 'Johansson'),
    (31, 'Bruce', 'Wayne'),
    (32, 'Clark', 'Kent'),
    (33, 'Diana', 'Ross'),
    (34, 'Elliot', 'Page'),
    (35, 'Freddie', 'Mercury'),
    (36, 'Gal', 'Gadot'),
    (37, 'Henry', 'Cavill'),
    (38, 'Isla', 'Fisher'),
    (39, 'James', 'Bond'),
    (40, 'Kate', 'Winslet');


INSERT INTO Members (email, password, userId)
VALUES
    ('alice@example.com', 'password1', 1),
    ('bob@example.com', 'password2', 2),
    ('charlie@example.com', 'password3', 3),
    ('diana@example.com', 'password4', 4),
    ('ethan@example.com', 'password5', 5),
    ('fiona@example.com', 'password6', 6),
    ('george@example.com', 'password7', 7),
    ('hannah@example.com', 'password8', 8),
    ('ian@example.com', 'password9', 9),
    ('jack@example.com', 'password10', 10),
    ('karen@example.com', 'password11', 11),
    ('leo@example.com', 'password12', 12),
    ('martha@example.com', 'password13', 13),
    ('nathan@example.com', 'password14', 14),
    ('olivia@example.com', 'password15', 15),
    ('paul@example.com', 'password16', 16),
    ('quincy@example.com', 'password17', 17),
    ('rachel@example.com', 'password18', 18),
    ('steve@example.com', 'password19', 19),
    ('tony@example.com', 'password20', 20),
    ('uma@example.com', 'password21', 21),
    ('victor@example.com', 'password22', 22),
    ('will@example.com', 'password23', 23),
    ('xander@example.com', 'password24', 24),
    ('yasmine@example.com', 'password25', 25),
    ('zack@example.com', 'password26', 26),
    ('liam@example.com', 'password27', 27),
    ('emma@example.com', 'password28', 28),
    ('chris@example.com', 'password29', 29),
    ('scarlett@example.com', 'password30', 30);


INSERT INTO Booking (bookingId, is_recurring, location, awaiting_response, creator)
VALUES
    (1, 1, 'Conference Room A', 0, 1),
    (2, 1, 'Meeting Room B', 0, 2),
    (3, 1, 'Lecture Hall C', 0, 3),
    (4, 1, 'Auditorium D', 0, 4),
    (5, 1, 'Room E', 0, 5),
    (6, 0, 'Library F', 1, 6),
    (7, 0, 'Room G', 1, 7),
    (8, 0, 'Room H', 1, 8),
    (9, 0, 'Room I', 1, 9),
    (10, 0, 'Room J', 1, 10),
    (11, 0, 'Room K', 1, 11),
    (12, 0, 'Room L', 1, 12),
    (13, 0, 'Room M', 1, 13),
    (14, 0, 'Room N', 1, 14),
    (15, 0, 'Room O', 1, 15);


INSERT INTO AvailableSlots (bookingId, week_day, start_time, end_time, max_participants, is_full)
VALUES
-- Booking 1 (Recurring)
(1, 'Monday', '09:00:00', '10:00:00', 10, 0),
(1, 'Wednesday', '10:00:00', '11:00:00', 15, 0),

-- Booking 2 (Recurring)
(2, 'Tuesday', '14:00:00', '15:30:00', 20, 0),
(2, 'Thursday', '16:00:00', '17:30:00', 10, 0),

-- Booking 3 (Recurring)
(3, 'Friday', '12:00:00', '13:00:00', 25, 0),
(3, 'Monday', '13:30:00', '14:30:00', 20, 0),

-- Booking 4 (Recurring)
(4, 'Thursday', '15:00:00', '16:30:00', 15, 0),

-- Booking 5 (Recurring)
(5, 'Monday', '11:00:00', '12:00:00', 12, 0),
(5, 'Wednesday', '13:00:00', '14:00:00', 8, 0),

-- Booking 6 (One-Time)
(6, 'Tuesday', '10:00:00', '11:00:00', 10, 0),

-- Booking 7 (One-Time)
(7, 'Thursday', '15:30:00', '16:30:00', 20, 0),

-- Booking 8 (One-Time)
(8, 'Friday', '09:00:00', '10:30:00', 15, 0),

-- Booking 9 (One-Time)
(9, 'Monday', '14:00:00', '15:30:00', 5, 0),

-- Booking 10 (One-Time)
(10, 'Wednesday', '10:00:00', '11:30:00', 18, 0),

-- Booking 11 (One-Time)
(11, 'Friday', '13:00:00', '14:30:00', 25, 0),

-- Booking 12 (One-Time)
(12, 'Tuesday', '15:00:00', '16:00:00', 20, 0),

-- Booking 13 (One-Time)
(13, 'Monday', '16:00:00', '17:00:00', 30, 0),

-- Booking 14 (One-Time)
(14, 'Thursday', '08:00:00', '09:00:00', 10, 0),

-- Booking 15 (One-Time)
(15, 'Friday', '17:00:00', '18:00:00', 5, 0);


INSERT INTO Dates (slotId, start_date, end_date)
VALUES
    -- Booking 1 (Recurring)
    (1, '2024-01-01', '2024-03-31'),
    (2, '2024-01-01', '2024-03-31'),

    -- Booking 2 (Recurring)
    (3, '2024-01-01', '2024-03-31'),
    (4, '2024-01-01', '2024-03-31'),

    -- Booking 3 (Recurring)
    (5, '2024-01-01', '2024-03-31'),
    (6, '2024-01-01', '2024-03-31'),

    -- Booking 4 (Recurring)
    (7, '2024-01-01', '2024-03-31'),

    -- Booking 5 (Recurring)
    (8, '2024-01-01', '2024-03-31'),
    (9, '2024-01-01', '2024-03-31'),

    -- Booking 6 to 15 (One-Time Bookings)
    (10, '2024-01-10', NULL),
    (11, '2024-01-12', NULL),
    (12, '2024-01-14', NULL),
    (13, '2024-01-16', NULL),
    (14, '2024-01-18', NULL),
    (15, '2024-01-20', NULL),
    (16, '2024-01-22', NULL),
    (17, '2024-01-24', NULL),
    (18, '2024-01-26', NULL),
    (19, '2024-01-28', NULL);


INSERT INTO Reserve (bookingId, userId, slotId)
VALUES
    -- Reservations for Booking 1
    (1, 1, 1),
    (1, 2, 2),

    -- Reservations for Booking 2
    (2, 3, 3),
    (2, 4, 4),

    -- Reservations for Booking 3
    (3, 5, 5),
    (3, 6, 6),

    -- Reservations for Booking 4
    (4, 7, 7),

    -- Reservations for Booking 5
    (5, 8, 8),
    (5, 9, 9),

    -- Reservations for One-Time Bookings (6 to 15)
    (6, 10, 10),
    (7, 11, 11),
    (8, 12, 12),
    (9, 13, 13),
    (10, 14, 14),
    (11, 15, 15),
    (12, 16, 16),
    (13, 17, 17),
    (14, 18, 18),
    (15, 19, 19);


INSERT INTO Poll (pollId, description, title, status, creator)
VALUES
    (1, 'Vote for meeting time.', 'Meeting Time', 1, 1),
    (2, 'Vote for location.', 'Location', 1, 2),
    (3, 'Vote for project lead.', 'Project Lead', 1, 3),
    (4, 'Vote for event date.', 'Event Date', 1, 4),
    (5, 'Vote for retreat theme.', 'Retreat Theme', 1, 5);

INSERT INTO Votes (pollId, userId, day, slot_number)
VALUES
    (1, 1, 'Monday', 1),
    (1, 2, 'Monday', 2),
    (1, 3, 'Monday', 3),
    (1, 4, 'Monday', 4),
    (1, 5, 'Monday', 5),
    (1, 6, 'Monday', 6),
    (1, 7, 'Monday', 7),
    (1, 8, 'Monday', 8),
    (1, 9, 'Monday', 9),
    (1, 10, 'Monday', 10);

INSERT INTO NotificationTemplate (templateId, message)
VALUES
    (1, 'You have a new poll.'),
    (2, 'A new booking has been made.'),
    (3, 'Meeting reminder.'),
    (4, 'Poll results are ready.'),
    (5, 'Your booking is confirmed.');

INSERT INTO Notification (notificationId, date, status_read, userId, templateId)
VALUES
    (1, '2024-01-01', 0, 1, 1),
    (2, '2024-01-02', 0, 2, 2),
    (3, '2024-01-03', 0, 3, 3),
    (4, '2024-01-04', 0, 4, 4),
    (5, '2024-01-05', 0, 5, 5),
    (6, '2024-01-06', 0, 6, 1),
    (7, '2024-01-07', 0, 7, 2);


