create database if not exists `Cronose` character set UTF8 collate utf8_spanish_ci;
use `Cronose`;
set sql_mode = 'allow_invalid_dates';

CREATE TABLE IF NOT EXISTS `Language` (
    id varchar(2) PRIMARY KEY NOT NULL
)ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `Languages_Offerted` (
    id varchar(2) PRIMARY KEY NOT NULL
)ENGINE = InnoDB;

create table if not exists `Languages_Translation` (
    language_id varchar(2) not null,
    language_translated varchar(2) not null,
    translation varchar(25) not null,
    foreign key (language_id) references `Language`(id),
    foreign key (language_translated) references `Languages_Offerted`(id)
) ENGINE = InnoDB;

create table if not exists `Province` (
    id int auto_increment primary key not null,
    name varchar(25) not null
) ENGINE = InnoDB;

create table if not exists `City` (
    cp int not null unique,
    province_id int not null,
    name varchar(25) not null,
    longitude double(13,10) not null,
    latitude double(13,10) not null,
    foreign key (province_id) references `Province`(id),
    primary key(cp, province_id)
)ENGINE = InnoDB;

create table if not exists `Company` (
    id int auto_increment primary key not null,
    name varchar(85) not null,
    phone varchar(50),
    email varchar(100),
    website varchar(255)
)ENGINE = InnoDB;

create table if not exists `Category` (
    id int auto_increment primary key not null,
    coin_price double(2,1) not null
)ENGINE = InnoDB;

create table if not exists `Category_Language` (
    language_id varchar(2) not null,
    category_id int not null,
    name varchar(45) not null,
    foreign key (language_id) references `Language`(id),
    foreign key (category_id) references `Category`(id),
    primary key(language_id, category_id)
)ENGINE = InnoDB;

create table if not exists `Specialization` (
    id int auto_increment primary key not null,
    category_id int not null,
    foreign key (category_id) references `Category`(id)
)ENGINE = InnoDB;

create table if not exists `Specialization_Language` (
    language_id varchar(2) not null,
    specialization_id int not null,
    name varchar(65) not null,
    foreign key (language_id) references `Language`(id),
    foreign key (specialization_id) references `Specialization`(id),
    primary key(language_id, specialization_id)
)ENGINE = InnoDB;

create table if not exists `Cancelation_Policy` (
    id int auto_increment primary key not null
)ENGINE = InnoDB;

create table if not exists `Cancelation_Language` (
    language_id varchar(2) not null,
    cancelation_policy_id int not null,
    name varchar(25) not null,
    description varchar(255) not null,
    foreign key (language_id) references `Language`(id),
    foreign key (cancelation_policy_id) references `Cancelation_Policy`(id),
    primary key(language_id, cancelation_policy_id)
)ENGINE = InnoDB;

create table if not exists `Cancelation_Section` (
    id int auto_increment primary key not null,
    cancelation_compensation double(2, 2) not null,
    cancelation_zone double(2, 2) not null
)ENGINE = InnoDB;

create table if not exists `Cancelation_Integrates_Section` (
    cancelation_policy_id int not null,
    cancelation_section_id int not null,
    foreign key (cancelation_policy_id) references `Cancelation_Policy`(id),
    foreign key (cancelation_section_id) references `Cancelation_Section`(id),
    primary key(cancelation_policy_id, cancelation_section_id)
)ENGINE = InnoDB;

create table if not exists `Media` (
    id int auto_increment primary key not null,
    extension varchar(8) not null,
    url varchar(255) not null
)ENGINE = InnoDB;

create table if not exists `DNI_Photo` (
    id int auto_increment primary key not null,
    status enum('accepted', 'pending', 'rejected') default 'pending' not null,
    media_id int not null,
    foreign key (media_id) references `Media`(id)
)ENGINE = InnoDB;

create table if not exists `User` (
    id int auto_increment unique,
    dni varchar(9) not null unique,
    name varchar(45) not null,
    surname varchar(45) not null,
    surname_2 varchar(45),
    email varchar(32) not null unique,
    password varchar(255) not null,
    tag int(4) not null,
    initials varchar(5) not null,
    coins double(3,2) default '0.00' not null,
    registration_date date not null,
    points int default 0 not null,
    private boolean default false not null,
    city_cp int not null,
    province_id int not null,
    avatar_id int,
    dni_photo_id int not null,
    validated boolean not null default 0,
    description varchar(400),
    primary key (id),
    foreign key (city_cp) references `City`(cp),
    foreign key (province_id) references `Province`(id),
    foreign key (avatar_id) references `Media`(id),
    foreign key (dni_photo_id) references `DNI_Photo`(id)
)ENGINE = InnoDB;

create table if not exists `Token` (
    id int auto_increment primary key,
    user_id int not null,
    token varchar(200) not null,
    name enum('Restore_pswd', 'User_validate'),
    foreign key (user_id) references `User` (id)
);

create table if not exists `User_Language` (
    language_id varchar(2) not null,
    user_id int not null,
    foreign key (language_id) references `Language`(id),
    foreign key (user_id) references `User`(id),
    primary key(language_id, user_id)
)ENGINE = InnoDB;

create table if not exists `Blocks` (
    user_blocker_id int not null,
    user_blocked_id int not null,
    foreign key (user_blocker_id) references `User`(id),
    foreign key (user_blocked_id) references `User`(id),
    primary key(user_blocker_id, user_blocked_id)
)ENGINE = InnoDB;

create table if not exists `Message` (
    sender_id int not null,
    receiver_id int not null,
    sended_date timestamp not null unique,
    message varchar(400) not null,
    foreign key (sender_id) references `User`(id),
    foreign key (receiver_id) references `User`(id),
    primary key(sender_id, receiver_id, sended_date)
)ENGINE = InnoDB;

create table if not exists `Advertisement` (
    company_id int not null,
    specialization_id int not null,
    foreign key (company_id) references `Company`(id),
    foreign key (specialization_id) references `Specialization`(id),
    primary key (company_id, specialization_id)
)ENGINE = InnoDB;

create table if not exists `Advertisement_Language` (
    language_id varchar(2) not null,
    company_id int not null,
    specialization_id int not null,
    title varchar(155) not null,
    description varchar(255),
    foreign key (language_id) references `Language`(id),
    foreign key (company_id) references `Advertisement`(company_id),
    foreign key (specialization_id) references `Advertisement`(specialization_id),
    primary key(language_id, company_id, specialization_id)
)ENGINE = InnoDB;

create table if not exists `Published` (
    starting_date timestamp not null unique,
    company_id int not null,
    specialization_id int not null,
    ending_date date,
    foreign key (company_id) references `Company`(id),
    foreign key (specialization_id) references `Specialization`(id),
    primary key (starting_date, company_id, specialization_id)
)ENGINE = InnoDB;

create table if not exists `Published_In_City` (
    city_cp int not null,
    starting_date timestamp not null unique,
    company_id int not null,
    specialization_id int not null,
    foreign key (company_id) references `Company`(id),
    foreign key (specialization_id) references `Specialization`(id),
    foreign key (city_cp) references `City`(cp),
    primary key (city_cp, starting_date, company_id, specialization_id)
)ENGINE = InnoDB;

create table if not exists `Illustrates` (
    media_id int,
    company_id int not null,
    specialization_id int not null,
    foreign key (media_id) references `Media`(id),
    foreign key (company_id) references `Company`(id),
    foreign key (specialization_id) references `Specialization`(id),
    primary key (media_id, company_id, specialization_id)
)ENGINE = InnoDB;

create table if not exists `Achievement` (
    id int not null auto_increment primary key
)ENGINE = InnoDB;

create table if not exists `Achievement_Language` (
    language_id varchar(2) not null,
    achievement_id int not null,
    name varchar(45) not null,
    description varchar(255) not null,
    foreign key (language_id) references `Language`(id),
    foreign key (achievement_id) references `Achievement`(id),
    primary key(language_id, achievement_id)
)ENGINE = InnoDB;

create table if not exists `Obtain` (
    achievement_id int not null,
    user_id int not null,
    obtained_at date not null,
    foreign key (achievement_id) references `Achievement`(id),
    foreign key (user_id) references `User`(id),
    primary key(achievement_id, user_id)
)ENGINE = InnoDB;

create table if not exists `Seniority` (
    level int not null primary key,
    points int not null,
    debt_quantity int not null
)ENGINE = InnoDB;

create table if not exists `Seniority_Language`(
    language_id varchar(2) not null,
    level_id int not null,
    name varchar(45) not null,
    foreign key (language_id) references `Language`(id),
    foreign key (level_id) references `Seniority`(level),
    primary key(language_id, level_id)
)ENGINE = InnoDB;

create table if not exists `Change_Seniority` (
    seniority_level int not null,
    user_id int not null,
    changed_at date not null,
    foreign key (seniority_level) references `Seniority`(level),
    foreign key (user_id) references `User`(id),
    primary key (seniority_level, user_id)
)ENGINE = InnoDB;

create table if not exists `Offer` (
    user_id int not null,
    specialization_id int not null,
    valoration_avg int(3) default 0 not null,
    personal_valoration int(3) default 0 not null,
    coin_price double(2,1) not null,
    offered_at date not null,
    visibility boolean default true not null,
    foreign key (user_id) references `User`(id),
    foreign key (specialization_id) references `Specialization`(id),
    primary key (user_id, specialization_id)
)ENGINE = InnoDB;

create table if not exists `Offer_Language`(
    language_id varchar(2) not null,
    user_id int not null,
    specialization_id int not null,
    title varchar(45) not null,
    description varchar(255) not null,
    foreign key (language_id) references `Languages_Offerted`(id),
    foreign key (user_id) references `Offer`(user_id),
    foreign key (specialization_id) references `Offer`(specialization_id),
    primary key(language_id, user_id, specialization_id)
)ENGINE = InnoDB;

create table if not exists `Promotes` (
    user_id int not null,
    specialization_id int not null,
    starting_date timestamp not null unique,
    ending_date datetime,
    foreign key (user_id) references `User`(id),
    foreign key (specialization_id) references `Specialization`(id),
    primary key (user_id, specialization_id, starting_date)
)ENGINE = InnoDB;

create table if not exists `Load_Media` (
    user_id int not null,
    specialization_id int not null,
    media_id int not null,
    foreign key (user_id) references `User`(id),
    foreign key (specialization_id) references `Specialization`(id),
    foreign key (media_id) references `Media`(id),
    primary key (user_id, specialization_id, media_id)
)ENGINE = InnoDB;

create table if not exists `QR_Code` (
    id int auto_increment primary key not null,
    url varchar(255) not null,
    status enum('pending','accepted','done') default 'pending' not null
)ENGINE = InnoDB;

create table if not exists `Demands` (
	id int auto_increment primary key not null,
    client_id int not null,
    worker_id int not null,
    specialization_id int not null,
    demanded_at timestamp not null unique,
    foreign key (client_id) references `User`(id),
    foreign key (worker_id) references `Offer`(user_id),
    foreign key (specialization_id) references `Offer`(specialization_id),
    unique key (client_id, worker_id, specialization_id, demanded_at)
)ENGINE = InnoDB;


create table if not exists `Card` (
    id int auto_increment primary key not null,
    status enum('pending','accepted','done','rejected') default 'pending' not null,
    work_date datetime not null,
    qr_code_id int,
    cancelation_policy_id int not null,
    demand_id int not null,
    foreign key (qr_code_id) references `QR_Code`(id),
    foreign key (cancelation_policy_id) references `Cancelation_Policy`(id),
    foreign key (demand_id) references `Demands`(id)
)ENGINE = InnoDB;


create table if not exists `Changes_Suggested` (
    id double(3,2) primary key not null,
    id_card int not null,
    old_date timestamp not null,
    suggested_date timestamp not null,
    foreign key (id_card) references `Card`(id)
)ENGINE = InnoDB;


create table if not exists `Auction` (
    user_id int not null,
    specialization_id int not null,
    auctioned_at timestamp not null unique,
    valoration_avg int default 0 not null,
    personal_valoration int default 0 not null,
    start_coin_price double(2,1) not null,
    work_date date not null,
    cancelation_policy_id int not null,
    card_id int not null,
    foreign key (user_id) references `User`(id),
    foreign key (specialization_id) references `Specialization`(id),
    foreign key (cancelation_policy_id) references `Cancelation_Policy`(id),
    foreign key (card_id) references `Card`(id),
    primary key (user_id, specialization_id, auctioned_at)
)ENGINE = InnoDB;

create table if not exists `Auction_Language` (
    language_id varchar(2) not null,
    user_id int not null,
    specialization_id int not null,
    auctioned_at timestamp not null unique,
    title varchar(45) not null,
    description varchar(255) not null,
    foreign key (language_id) references `Language`(id),
    foreign key (user_id) references `Auction`(user_id),
    foreign key (specialization_id) references `Auction`(specialization_id),
    foreign key (auctioned_at) references `Auction`(auctioned_at),
    primary key(language_id, user_id, specialization_id, auctioned_at)
)ENGINE = InnoDB;

create table if not exists `Pushes` (
    coin_pushed double(3,2) not null,
    date_pushed timestamp not null unique,
    pusher_id int not null,
    user_id int not null,
    specialization_id int not null,
    auction_date timestamp not null unique,
    foreign key (pusher_id) references `User`(id),
    foreign key (user_id) references `Auction`(user_id),
    foreign key (specialization_id) references `Auction`(specialization_id),
    foreign key (auction_date) references `Auction`(auctioned_at),
    primary key(pusher_id, user_id, specialization_id, auction_date, date_pushed)
)ENGINE = InnoDB;

create table if not exists `Valoration_Label` (
    id int auto_increment primary key not null
)ENGINE = InnoDB;

create table if not exists `Valoration_Label_Language`(
    language_id varchar(2) not null,
    valoration_label_id int not null,
    aspect varchar(45) not null,
    foreign key (language_id) references `Language`(id),
    foreign key (valoration_label_id) references `Valoration_Label`(id),
    primary key(language_id, valoration_label_id)
)ENGINE = InnoDB;

create table if not exists `Comment` (
    id int auto_increment primary key not null
)ENGINE = InnoDB;

create table if not exists `Comment_Language`(
    language_id varchar(2) not null,
    comment_id int not null,
    text varchar(255) not null,
    foreign key (language_id) references `Languages_Offerted`(id),
    foreign key (comment_id) references `Comment`(id),
    primary key(language_id, comment_id)
)ENGINE = InnoDB;

create table if not exists `Worker_Valoration` (
    valoration_id int not null,
    card_id int not null,
    comment_id int null,
    puntuation int not null,
    foreign key (valoration_id) references `Valoration_Label`(id),
    foreign key (card_id) references `Card`(id),
    foreign key (comment_id) references `Comment`(id),
    primary key (valoration_id, card_id)
)ENGINE = InnoDB;

create table if not exists `Client_Valoration` (
    valoration_id int not null,
    card_id int not null,
    comment_id int null,
    puntuation int not null,
    foreign key (valoration_id) references `Valoration_Label`(id),
    foreign key (card_id) references `Card`(id),
    foreign key (comment_id) references `Comment`(id),
    primary key (valoration_id,card_id)
)ENGINE = InnoDB;
