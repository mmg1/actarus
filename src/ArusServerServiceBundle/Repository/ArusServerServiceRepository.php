<?php

namespace ArusServerServiceBundle\Repository;

use Actarus\Utils;


/**
 * ArusServerServiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArusServerServiceRepository extends \Doctrine\ORM\EntityRepository
{
	public function search( $data, $offset=null, $limit=null )
	{
		$data = Utils::array2object( $data, 'ArusServerServiceBundle\Entity\Search' );
		$t_params = array();
		$qb = $this->_em->createQueryBuilder();

		if( $offset < 0 ) {
			$offset = null;
			$count  = true;
			$query  = $qb->select( 'count(ss.id)' );
		} else {
			$count  = false;
			$query  = $qb->select( array('ss,s') );
		}
		$query = $query->from('ArusServerServiceBundle:ArusServerService','ss')
						->leftJoin('ss.server','s');

		if( $data )
		{
			if( $data->getServer() ) {
				$query->andWhere('ss.server=:server_id');
				$t_params['server_id'] = $data->getServer()->getId();
			}
			if( ($port=$data->getPort()) ) {
				if( !is_array($port) ) {
					$port = [ $port, '=' ];
				}
				$query->andWhere('ss.port '.$port[1].' :port');
				$t_params['port'] = $port[0];
			}
			if( ($service=$data->getService()) ) {
				if( !is_array($service) ) {
					$service = [ '%'.$service.'%', 'LIKE' ];
				}
				$query->andWhere('ss.service '.$service[1].' :service');
				$t_params['service'] = $service[0];
			}
		}

		$query->setParameters( $t_params );
		$query->orderBy('ss.id', 'DESC');
		if( !is_null($limit) ) {
			$query->setMaxResults( $limit );
		}
		if( !is_null($offset) ) {
			$query->setFirstResult($offset);
		}

		$t_result = $query->getQuery()->getResult();

		if( $count ) {
			return (int)$t_result[0][1];
		} else {
			return $t_result;
		}
	}
}
