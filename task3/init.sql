CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,             
    created TIMESTAMP WITH TIME ZONE   
        DEFAULT CURRENT_TIMESTAMP,
    name VARCHAR(255) NOT NULL,        
    email VARCHAR(255) NOT NULL,                
    phone VARCHAR(11) NOT NULL,               
    comment VARCHAR(1000) NOT NULL          
);