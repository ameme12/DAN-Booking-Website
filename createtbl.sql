CREATE TABLE User
(
    userId INT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
);

CREATE TABLE Members
(
    email VARCHAR(100) PRIMARY KEY,
    password VARCHAR(50) NOT NULL,
    FOREIGN KEY (userId) REFERENCES User(userId)
);

CREATE TABLE Booking
(
    bookingId INT PRIMARY KEY,
    is_recurring BIT NOT NULL,
    location VARCHAR(100),
    awaiting_response BIT,
    creator INT NOT NULL,
    FOREIGN KEY (creator) REFERENCES Members(userId)
);

CREATE TABLE Poll
(
    pollId INT PRIMARY KEY,
    description VARCHAR(max),
    title VARCHAR(50),
    status BIT,
    creator INT NOT NULL,
    FOREIGN KEY (creator) REFERENCES Members(userId)
);

CREATE TABLE Notification
(
    notificationId INT PRIMARY KEY,
    date DATE,
    message VARCHAR(MAX),
    status_read BIT,
    notified_user INT NOT NULL,
    FOREIGN KEY (notified_user) REFERENCES Members(userId)
);

CREATE TABLE Attachments
(
    attachmentId INT PRIMARY KEY,
    name VARCHAR(100),
    url VARCHAR(255),
);

CREATE TABLE Days
(
    week_day VARCHAR(50) PRIMARY KEY,
);

CREATE TABLE AvailableSlots
(
    week_day VARCHAR(20),
    slot_number INT,
    max_participants INT,
    full BIT,
    PRIMARY KEY (week_day, slot_number),
    FOREIGN KEY (week_day) REFERENCES Days(week_day)
);

CREATE TABLE Dates
(
    bookingId INT,
    week_day VARCHAR(20),
    start_date DATE NOT NULL,
    end_date DATE,
    PRIMARY KEY(bookingId, week_day),
    FOREIGN KEY (bookingId) REFERENCES Booking(bookingId),
    FOREIGN KEY (week_day) REFERENCES Days(week_day)

);

CREATE TABLE Reserve
(
    bookingId INT,
    userId INT,
    day VARCHAR(50),
    slot_number INT,
    PRIMARY KEY (bookingId, userId),
    FOREIGN KEY (bookingId) REFERENCES Booking(bookingId),
    FOREIGN KEY (userId) REFERENCES User(userId)
);

CREATE TABLE Votes
(
    pollId INT,
    userId INT,
    day VARCHAR(20),
    slot_number INT,
    PRIMARY KEY (pollId, userId),
    FOREIGN KEY (pollId) REFERENCES Poll(pollId),
    FOREIGN KEY (userId) REFERENCES Members(userId)
);

CREATE TABLE BookingAttachment
(
    bookingId INT,
    attachmentId INT NOT NULL,
    PRIMARY KEY (bookingId)
);












