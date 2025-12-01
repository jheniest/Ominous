@extends('errors::minimal')

@section('title', 'Conteúdo Muito Grande')
@section('code', '413')
@section('message', 'Arquivo Muito Grande')
@section('description', 'O arquivo que você está tentando enviar excede o tamanho máximo permitido.')
