<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class AutoUpdateWebSocket implements MessageComponentInterface {
    protected $clients;
    private $updateInterval;
    private $server;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->updateInterval = 5; // Envoi auto toutes les 3 secondes
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nouveau client connecté ({$conn->resourceId})\n";
        
        if ($this->clients->count() === 1) {
            $this->startAutoBroadcast();
        }
    }

    private function startAutoBroadcast() {
        $this->server->loop->addPeriodicTimer($this->updateInterval, function () {
            $this->broadcastUpdate();
        });
    }

    private function broadcastUpdate() {
        $updateData = [
            'type' => 'auto_update',
            'timestamp' => time(),
            'data' => $this->generateUpdateData()
        ];
        
        $message = json_encode($updateData);
        
        foreach ($this->clients as $client) {
            try {
                $client->send($message);
            } catch (\Exception $e) {
                echo "Erreur d'envoi: {$e->getMessage()}\n";
            }
        }
        
        echo "Mise à jour diffusée à ".$this->clients->count()." clients\n";
    }

    private function generateUpdateData() {
        // Exemple : générer des données dynamiques
        return [
            'niveau_batterie' => rand(10, 100),
            'status' => ['actif', 'inactif', 'maintenance'][rand(0, 2)],
            'devices_connectes' => rand(1, 15)
        ];
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message reçu de {$from->resourceId}: $msg\n";
        
        // Traitement des commandes spéciales
        if ($msg === 'get_update') {
            $this->broadcastUpdate();
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Client déconnecté ({$conn->resourceId})\n";
        
        if ($this->clients->count() === 0) {
            // Optionnel: arrêter le timer quand aucun client
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erreur: {$e->getMessage()}\n";
        $conn->close();
    }

    public function setServer(IoServer $server) {
        $this->server = $server;
    }
}

// Configuration du serveur
$port = 8061;
$wsServer = new AutoUpdateWebSocket();
$server = IoServer::factory(
    new HttpServer(
        new WsServer($wsServer)
    ),
    $port,
    '0.0.0.0'
);

$wsServer->setServer($server);
echo "Serveur WebSocket démarré sur ws://0.0.0.0:$port\n";
echo "Envoi automatique toutes les 3 secondes\n";
$server->run();