CREATE TABLE services (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(100) NOT NULL,
                          duration_minutes INT NOT NULL, -- Ex: 30, 60
                          price DECIMAL(10, 2) NOT NULL
);

CREATE TABLE availability_rules (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    day_of_week INT NOT NULL, -- 0 = Domingo, 1 = Segunda...
                                    start_time TIME NOT NULL, -- Ex: '09:00:00'
                                    end_time TIME NOT NULL    -- Ex: '18:00:00'
);

CREATE TABLE appointments (
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              service_id INT NOT NULL,
                              client_name VARCHAR(100),
                              client_email VARCHAR(100),
                              start_at DATETIME NOT NULL, -- Sempre em UTC
                              end_at DATETIME NOT NULL,   -- Sempre em UTC
                              status ENUM('pending', 'confirmed', 'canceled') DEFAULT 'pending',
                              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Inserindo dados de teste
INSERT INTO services (name, duration_minutes, price) VALUES ('Corte de Cabelo', 30, 50.00);
INSERT INTO availability_rules (day_of_week, start_time, end_time) VALUES (1, '09:00', '18:00'); -- Segunda-feira