Docker MariaDB Galera Cluster Setting

- 같은 네트워크 공유를 위해 갈레라 전용 도커 네트워크 설정 
  - docker network create galera-net
 

- Galera의 경우 클러스터 사이즈 홀수(3, 5, 7) 기반 세팅
  - docker run -d --name mariadb1 --network galera-net -e MYSQL_ROOT_PASSWORD=root -p 3306:3306 mariadb:10.6 --wsrep-new-cluster
  - docker run -d --name mariadb2 --network galera-net -e MYSQL_ROOT_PASSWORD=root -p 3307:3306 mariadb:10.6 --wsrep_cluster_address="gcomm://mariadb1"
  - docker run -d --name mariadb3 --network galera-net -e MYSQL_ROOT_PASSWORD=root -p 3308:3306 mariadb:10.6 --wsrep_cluster_address="gcomm://mariadb1"
 
- Container 접속 및 galera.cnf 설정
  - docker exec -it {container-name} bash mysql -u root -p
  - apt-get update && apt-get install -y vim
  - vi /etc/mysql/conf.d/galera.cnf
  - 아래 galera.cnf 파일 작성 후 저장
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
    wsrep_cluster_address="gcomm://{container-name},{container-name},{container-name}"

    # Galera Synchronization Configuration
    wsrep_sst_method=rsync

    # Galera Node Configuration
    wsrep_node_address="{current-container-name}"
    wsrep_node_name="{current_container-name}"
  - exit;
  - docker restart {container-name}
 
- 클러스 사이즈 확인 및 상태 확인 Query
  - SHOW STATUS LIKE 'wsrep_cluster_size';
  - SHOW STATUS LIKE 'wsrep_cluster_status';
