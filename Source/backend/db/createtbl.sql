
CREATE TABLE User
(
    userId INT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL
);

CREATE TABLE Members
(
    email VARCHAR(100) PRIMARY KEY,
    password VARCHAR(50) NOT NULL,
    userId INT NOT NULL UNIQUE,
    FOREIGN KEY (userId) REFERENCES User(userId)
);

CREATE TABLE Booking
(
    bookingId INT PRIMARY KEY,
    is_recurring BOOLEAN DEFAULT false,
    location VARCHAR(100),
    awaiting_response BOOLEAN,
    creator INT NOT NULL,
    FOREIGN KEY (creator) REFERENCES Members(userId)
);

CREATE TABLE Poll
(
    pollId INT PRIMARY KEY,
    description VARCHAR(500),
    title VARCHAR(50),
    status BOOLEAN DEFAULT TRUE,
    creator INT NOT NULL,
    FOREIGN KEY (creator) REFERENCES Members(userId)
);

CREATE TABLE NotificationTemplate
(
    templateId INT PRIMARY KEY,
    message VARCHAR(500)
);

CREATE TABLE Notification
(
    notificationId INT PRIMARY KEY,
    date DATE,
    status_read BOOLEAN DEFAULT false,
    userId INT NOT NULL,
    templateId INT NOT NULL,
    FOREIGN KEY (userId) REFERENCES Members(userId) ON DELETE CASCADE,
    FOREIGN KEY (templateId) REFERENCES NotificationTemplate(templateId)
);

CREATE TABLE Attachments
(
    attachmentId INT PRIMARY KEY,
    name VARCHAR(100),
    url VARCHAR(255)
);

CREATE TABLE AvailableSlots
(
    slotId INT AUTO_INCREMENT PRIMARY KEY,
    bookingId INT NOT NULL,
    week_day VARCHAR(20) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    max_participants INT NOT NULL,
    is_full BOOLEAN default false,
    FOREIGN KEY (bookingID) REFERENCES Booking(bookingId)
);

CREATE TABLE Dates
(
    slotId INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    PRIMARY KEY(slotId),
    FOREIGN KEY (slotId) REFERENCES AvailableSlots(slotId)

);

CREATE TABLE Reserve
(
    reserveId INT AUTO_INCREMENT PRIMARY KEY,
    bookingId INT NOT NULL,
    userId INT NOT NULL,
    slotId INT NOT NULL,
    FOREIGN KEY (bookingId) REFERENCES Booking(bookingId) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES User(userId),
    FOREIGN KEY (slotId) REFERENCES AvailableSlots(slotId) ON DELETE CASCADE
);

CREATE TABLE Votes
(
    voteId INT AUTO_INCREMENT PRIMARY KEY,
    pollId INT NOT NULL,
    userId INT NOT NULL,
    day VARCHAR(20) NOT NULL ,
    slot_number INT NOT NULL,
    FOREIGN KEY (pollId) REFERENCES Poll(pollId) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES Members(userId) ON DELETE CASCADE
);

CREATE TABLE BookingAttachment
(
    bookingId INT,
    attachmentId INT NOT NULL,
    PRIMARY KEY (bookingId, attachmentId),
    FOREIGN KEY (bookingId) REFERENCES Booking(bookingId) ON DELETE CASCADE,
    FOREIGN KEY (attachmentId) REFERENCES Attachments(attachmentId) ON DELETE CASCADE

);

CREATE INDEX idx_members_email ON Members(email);
CREATE INDEX idx_booking_creator ON Booking(creator);
CREATE INDEX idx_reserve_userId ON Reserve(userId);
CREATE INDEX idx_votes_pollId ON Votes(pollId);








