# Docker MariaDB Galera Cluster Setting

- 같은 네트워크 공유를 위해 갈레라 전용 도커 네트워크 설정 및 컨테이너들이 같은 네트워크에서 서로 원할한 접근
  ```sh
  docker network create galera-net
  ```

- Galera의 경우 클러스터 사이즈 홀수(3, 5, 7) 기반 세팅
  ```sh
  docker run -d --name mariadb1 --network galera-net -e MYSQL_ROOT_PASSWORD=root -p 3306:3306 mariadb:10.6 --wsrep-new-cluster --wsrep_cluster_address="gcomm://"
  docker run -d --name mariadb2 --network galera-net -e MYSQL_ROOT_PASSWORD=root -p 3307:3306 mariadb:10.6 --wsrep_cluster_address="gcomm://mariadb1"
  docker run -d --name mariadb3 --network galera-net -e MYSQL_ROOT_PASSWORD=root -p 3308:3306 mariadb:10.6 --wsrep_cluster_address="gcomm://mariadb1"
  ```
 
- Container 접속 및 galera.cnf 설정
  ```sh
  docker exec -it {container-name} bash
  apt-get update && apt-get install -y vim
  vi /etc/mysql/conf.d/galera.cnf
  ```
- 아래 galera.cnf 파일 작성 후 저장
  ```ini
  [mysqld]
  binlog_format=ROW
  default-storage-engine=innodb
  innodb_autoinc_lock_mode=2
  bind-address=0.0.0.0

  # Galera Provider Configuration
  wsrep_on=ON
  wsrep_provider=/usr/lib/galera/libgalera_smm.so

  # Galera Cluster Configuration
  wsrep_cluster_name="galera_cluster"
  wsrep_cluster_address="gcomm://mariadb1,mariadb2,mariadb3" // 컨테이너 이름 및 ip address

  # Galera Synchronization Configuration
  wsrep_sst_method=rsync

  # Galera Node Configuration
  wsrep_node_address="{current-container-name}"
  wsrep_node_name="{current_container-name}"
  ```

  ```sh
  exit;
  docker restart {container-name}
  ```
 
- 클러스 사이즈 확인 및 상태 확인 Query
  ```mysql
  SHOW STATUS LIKE 'wsrep_cluster_size';
  SHOW STATUS LIKE 'wsrep_cluster_status';
  ```

- MariaDB Cluster All Exited 일 때 다시 복구 하는 방법 (첫 번째 MariaDB safe_to_boodstrap 설정)
  ```sh
  docker inspect mariadb1 | grep -i "Source"
  Source 뒤에 나오는 path Copy
  docker run --rm -it -v {Source Path Paste}:/var/lib/mysql mariadb:10.6 bash :: 임시 컨테이너 생성 해서 볼륨으로 접근
  vi /var/lib/mysql/grastate.dat && safe_to_bootstrap=1 변경 후 container exit;
  docker start mariadb1
  docker exec -it mariadb1 bash
  mysqld --wsrep-new-cluster --user=mysql
  docker start mariadb2 mariadb3
  docker exec -it mariadb1 mysql -u root -p -e "SHOW STATUS LIKE 'wsrep_cluster_size';"
  ```
  

