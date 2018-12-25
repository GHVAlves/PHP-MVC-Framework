<?php

namespace System\Core {

	use PDO;	  

	class Database {

		/**
		 * Connection properties
		 */
		private $databaseDriver;
		private $host;
		private $port;
		private $user;
		private $password;
		private $database;    
		private $connection;
		private $statement;

		/**
         * Build SQL
         */
        private $operation;
        private $sql;

        /**
         * Shared
         */
        private $where;
        private $andOr;

        /**
         * Insert
         */
        private $insert;

        /**
         * Update
         */
        private $update;

        /**
         * Select
         */
        private $columns;
        private $table;
        private $join;
        private $group;
        private $order;
        private $limit;

        /**
         * Delete
         */
        private $delete;

		public function __construct(string $driver = null, string $host = null, string $port = null, string $user = null, string $password = null, string $database = null) {


            if (!$host && !$port && !$user && !$password && !$database) {

                $ini = parse_ini_file(ROOT . DIRECTORY_SEPARATOR . SYSTEM_PATH . '/config.ini', true);

                $driver = $ini['database']['driver'];
                $host = $ini['database']['host'];
                $port = $ini['database']['port'];
                $user = $ini['database']['user'];
                $password = $ini['database']['password'];
                $database = $ini['database']['database'];

            }

			$this->databaseDriver = $driver;
			$this->host = $host;
			$this->port = $port;
			$this->user = $user;
			$this->password = $password;
            $this->database = $database;
            
            $this->open();

		}

		private function buildInsert($parameters) {

            $this->sql = $this->insert;

            $columns = array_keys($parameters);
            $values = array_values($parameters);

            for ($i = 0; $i < count($columns); $i++) { 
                
                if ($i == 0) {

                    $this->sql .= ' ( ' . $columns[$i] . ', ';

                }
                else if ($i == (count($columns) - 1)) {
                    
                    $this->sql .= $columns[$i] . ' ) ';

                }
                else {

                    $this->sql .= $columns[$i] . ', ';

                }

            }

            for ($i = 0; $i < count($values); $i++) { 
                
                if ($i == 0) {

                    $this->sql .= " VALUES ( ?, ";

                }
                else if ($i == (count($values) - 1)) {
                    
                    $this->sql .= "? ) ";

                }
                else {

                    $this->sql .= "?, ";

                }

            }

        }

        private function buildUpdate($parameters) {

            $this->sql = $this->update;

            $index = 0;

            foreach ($parameters as $key => $value) {

                if ($index == 0) {

                    $this->sql .= " SET $key = ?, ";

                }
                else if ($index == (count($parameters) - 1)) {
                    
                    $this->sql .= " $key = ? ";

                }
                else {

                    $this->sql .= " $key = ?, ";

                }

                $index++;

            }

            if ($this->where) {

                $this->sql .= $this->where;
                
            }

            if ($this->andOr) {

                $this->sql .= $this->andOr;

            }

        }

        private function buildSelect() {

            $this->sql = $this->columns . $this->table;
                       
            if ($this->join) {

                $this->sql .= $this->join;

            }

            if ($this->where) {

                $this->sql .= $this->where;
                
            }

            if ($this->andOr) {

                $this->sql .= $this->andOr;

            }

            if ($this->group) {

                $this->sql .= $this->group;

            }

            if ($this->order) {

                $this->sql .= $this->order;

            }

            if ($this->limit) {

                $this->sql .= $this->limit;

            }

        }

        private function buildDelete() {

            $this->sql = $this->delete;

            if ($this->where) {

                $this->sql .= $this->where;

            }

            if ($this->andOr) {

                $this->sql .= $this->andOr;

            }

        }

		public function open() {

			try {

				if ($this->databaseDriver === 'sqlsrv') {

					$this->connection = new PDO('sqlsrv:Server=' . (($this->port) ? $this->host . ':' . $this->port : $this->host) . ';Database=' . $this->database . ';ConnectionPooling=0"', $this->user, $this->password);
					$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

				}
				else if ($this->databaseDriver === 'mysql') {

					$this->connection = new PDO('mysql:dbname=' . $this->database . ';host=' . (($this->port) ? $this->host . ':' . $this->port : $this->host) . '"', $this->user, $this->password);
                    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

				}

			} catch (Exception $exception) {

				die('Failed to connect to Database:' . $exception->getMessage() . '.');

			}

		}

		public function close() {

			$this->connection = null;

		}

		public function getConnection() {

			return $this->connection;

		}

		public function bindParameters(Array $parameters = null) {

			if ($parameters && count($parameters) > 0) {

				$parameters = (($parameters != null && count($parameters) > 0) ? array_values($parameters) : $parameters);

				for ($i = 0; $i < count($parameters); $i++) {
					
					$value = trim($parameters[$i]);

					if (filter_var($value, FILTER_VALIDATE_INT) || $value === '0') {

						$this->statement->bindParam(($i + 1), $parameters[$i], PDO::PARAM_INT);

					}
					else if (filter_var($value, FILTER_VALIDATE_FLOAT)) {

						$this->statement->bindParam(($i + 1), $parameters[$i], PDO::PARAM_STR);

					}
					else {

                        if (!$parameters[$i]) {

                            $this->statement->bindParam(($i + 1), $parameters[$i], PDO::PARAM_NULL);

                        }
                        else {
                            
                            $this->statement->bindParam(($i + 1), $parameters[$i], PDO::PARAM_STR);

                        }                        					

					}

				}
				
			}

		}

		public function executeSQL(string $sql, Array $parameters = null) {

			try {

				$this->statement = $this->connection->prepare($sql);				
				$this->bindParameters($parameters);
				$this->statement->execute();

			}
			catch (Exception $exception) {

				throw $exception;

			}

		}

		public function executeInsert(string $sql, Array $parameters = null) {

			$insertedId = 0;

			try {

				$this->statement = $this->connection->prepare($sql);
				$this->bindParameters($parameters);
				$this->statement->execute();

				$insertedId = $this->connection->lastInsertId();

				return $insertedId;

			}
			catch (Exception $exception) {

				throw $exception;

			}			

		}

		public function executeSelect(string $sql, Array $parameters = null) {

			try {

				$this->statement = $this->connection->prepare($sql);
				$this->bindParameters($parameters);
				$this->statement->execute();                

				$result = $this->statement->fetchAll(PDO::FETCH_ASSOC);

				return $result;

			}
			catch (Exception $exception) {

				throw $exception;

			}

		}

		public function executeProcedure(string $name, Array $parameters = null) {

			$stringParameters = '';

			for ($i = 0; $i < count($parameters); $i++) {

				$stringParameters .= '?, ';

			}

			$stringParameters = substr($stringParameters, 0, -2);

			$sql = ' EXEC ' . $name . ' ' . $stringParameters;

			try {

				$this->open();

				$this->statement = $this->connection->prepare($sql);

				$this->bindParameters($parameters);
				$this->statement->execute();

				if ($this->statement->columnCount() <= 0) {

					$this->statement->nextRowset();

				}

				$result = $this->statement->fetchAll(PDO::FETCH_ASSOC);

				$this->close();

				return $result;

			}
			catch (Exception $exception) {

				throw $exception;

			}

		}

		public function insert(string $table) {

            $this->operation = 'insert';

            $this->insert = " INSERT INTO $table ";

            return $this;

        }

        public function update(string $table) {

            $this->operation = 'update';

            $this->update = " UPDATE $table ";

            return $this;

        }

        public function select(string $columns) {

            $this->operation = 'select';

            if (!$this->columns) {

                $this->columns = " SELECT $columns ";

            }
            else {

                $this->columns .= ", $columns ";

            }

            return $this;

		}

		public function from(string $table) {
            
            $this->table = " FROM $table ";

            return $this;
			
        }

        public function where() {

            $arguments = func_get_args();

            if (count($arguments) === 1) {

                $this->where = ' WHERE ' . $arguments[0];

            }
            else if (count($arguments) === 2) {

                $this->where = " WHERE " . $arguments[0] . " '" . $arguments[1] . "'";

            }

            return $this;

        }

        public function and() {

            $arguments = func_get_args();

            if (count($arguments) === 1) {

                $this->andOr .= ' AND ' . $arguments[0];

            }
            else if (count($arguments) === 2) {

                $this->andOr = " AND " . $arguments[0] . " '" . $arguments[1] . "'";

            }

            return $this;

        }

        public function or() {

            $arguments = func_get_args();

            if (count($arguments) === 1) {

                $this->andOr .= ' OR ' . $arguments[0];

            }
            else if (count($arguments) === 2) {

                $this->andOr = " OR " . $arguments[0] . " '" . $arguments[1] . "'";

            }

            return $this;

        }

        public function innerJoin(string $table, string $join) {

            $this->join .= " INNER JOIN $table ON $join ";

            return $this;

        }

        public function rightJoin(string $table, string $join) {

            $this->join .= " RIGHT JOIN $table ON $join ";

            return $this;

        }

        public function leftJoin(string $table, string $join) {

            $this->join .= " LEFT JOIN $table ON $join ";

            return $this;

        }

        public function group(string $group) {

            if (!$this->group) {

                $this->group = " GROUP BY $group ";

            }
            else {

                $this->group .= ", $group ";

            }

            return $this;

        }

        public function order(string $order) {

            if (!$this->order) {

                $this->order = " ORDER BY $order ";

            }
            else {

                $this->order .= ", $order ";

            }

            return $this;

        }

        public function limit(int $start, int $final) {

            $this->limit = " LIMIT $limit " . (($final) ? ", $final " : '');

            return $this;

        }

        public function delete(string $table) {

            $this->operation = 'delete';

            $this->delete = " DELETE FROM $table ";

            return $this;

        }
        
        public function clear() {

            /**
             * Build SQL
             */
            $this->operation = null;
            $this->sql = null;

            /**
             * Shared
             */
            $this->where = null;
            $this->andOr = null;

            /**
             * Insert
             */
            $this->insert = null;

            /**
             * Update
             */
            $this->update = null;

            /**
             * Select
             */
            $this->columns = null;
            $this->table = null;
            $this->join = null;
            $this->group = null;
            $this->order = null;
            $this->limit = null;

            $this->open();
            
        }

        public function exec(Array $parameters = null) {

            try {

                if ($this->operation === 'insert') {
                    
                    $this->buildInsert($parameters);                    

                    return $this->executeInsert($this->sql, $parameters);

                }
                if ($this->operation === 'update') {
                    
                    $this->buildUpdate($parameters);

                    return $this->executeSQL($this->sql, $parameters);

                }                
                else if ($this->operation === 'select') {

                    $this->buildSelect();

                    return $this->executeSelect($this->sql, $parameters);

                }                
                else if ($this->operation === 'delete') {

                    $this->buildDelete();

                    $this->executeSQL($this->sql, $parameters);
                    
                }

            }
            catch (Exception $exception) {

                throw $exception;

            }
            finally {
                
                $this->clear();
                
            }

        }

	}

}

?>
